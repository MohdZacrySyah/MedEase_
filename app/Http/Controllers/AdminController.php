<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Pendaftaran;
use App\Models\JadwalPraktek;
use App\Models\Admin;
use App\Models\User;
use App\Models\Pemeriksaan;
use App\Models\TenagaMedis;
use App\Models\DoctorAvailability;
use Illuminate\Support\Facades\DB;
use App\Models\Layanan;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function dashboard()
    {
        $user = Auth::guard('admin')->user();
        $today = Carbon::today();
        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');

        // Cek Dokter Libur Hari Ini
        $unavailableDoctorIds = DoctorAvailability::whereDate('date', $today)
            ->where('is_available', false)
            ->pluck('tenaga_medis_id')
            ->toArray();

        // HITUNG STATISTIK (KPI)
        $jumlahPasienHariIni = Pendaftaran::whereDate('jadwal_dipilih', $today)->count();
        $jumlahMenunggu = Pendaftaran::whereDate('jadwal_dipilih', $today)->where('status', 'Menunggu')->count();
        $jumlahSelesai = Pendaftaran::whereDate('jadwal_dipilih', $today)->where('status', 'Selesai')->count();
        $jumlahTenagaMedis = TenagaMedis::count();
        
        $jumlahTotalPasien = User::where('role', 'pasien')
            ->orWhereNull('role')
            ->count();

        // Hitung Dokter Aktif
        $jumlahDokterAktifHariIni = JadwalPraktek::whereJsonContains('hari', $namaHariIni)
            ->whereNotIn('tenaga_medis_id', $unavailableDoctorIds)
            ->distinct('tenaga_medis_id')
            ->count('tenaga_medis_id');
        
        $pemeriksaanTahunIni = Pemeriksaan::whereYear('created_at', Carbon::now()->year)->count();

        // AMBIL DATA UNTUK TABEL DASHBOARD
        $jadwalHariIni = JadwalPraktek::with('tenagaMedis')
            ->whereJsonContains('hari', $namaHariIni)
            ->whereNotIn('tenaga_medis_id', $unavailableDoctorIds)
            ->get();
                                      
        $pendaftaranMenunggu = Pendaftaran::with('user')
            ->whereDate('jadwal_dipilih', $today)
            ->where('status', 'Menunggu')
            ->orderBy('no_antrian', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user',
            'jumlahPasienHariIni',
            'jumlahMenunggu',
            'jumlahSelesai',
            'jumlahTenagaMedis',
            'jumlahTotalPasien',
            'jumlahDokterAktifHariIni',
            'pemeriksaanTahunIni',
            'jadwalHariIni',
            'pendaftaranMenunggu'
        ));
    }

    public function showLoginForm()
    {
        return view('admin.login'); 
    }
     
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau Password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
    
    public function keloladatapasien(Request $request)
    {
        $search = $request->input('search');
        $query = User::where(function ($q) {
            $q->where('role', 'pasien')->orWhereNull('role'); 
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $pasiens = $query->orderBy('created_at', 'desc')->paginate(8);

        return view('admin.keloladatapasien', compact('pasiens', 'search'));
    }

    // ==========================================
    // MENU CATATAN PEMERIKSAAN (ANTRIAN)
    // ==========================================
    public function catatanpemeriksaan(Request $request)
    {
        $tanggal = $request->input('tanggal'); 
        $targetDate = $tanggal ? $tanggal : Carbon::today();

        // 1. Ambil Layanan yang tersedia di jadwal
        $semuaLayanan = JadwalPraktek::select('layanan')
            ->distinct()
            ->orderBy('layanan', 'asc')
            ->pluck('layanan');

        // 2. Query Pendaftaran
        // Order by Status: Menunggu & Hadir duluan, baru Diperiksa Awal, lalu Selesai
        $query = Pendaftaran::with(['user', 'jadwalPraktek.tenagaMedis'])
            ->whereDate('jadwal_dipilih', $targetDate);

        $pendaftaransGrouped = $query
            ->orderByRaw("FIELD(status, 'Menunggu', 'Hadir', 'Diperiksa Awal', 'Selesai', 'Dibatalkan')")
            ->orderBy('no_antrian', 'asc')
            ->get()
            ->groupBy('nama_layanan');

        $pendaftarans = collect();
        foreach ($semuaLayanan as $layananName) {
            $pendaftarans[$layananName] = $pendaftaransGrouped->has($layananName) 
                ? $pendaftaransGrouped[$layananName] 
                : collect();
        }

        return view('admin.catatanpemeriksaan', compact('pendaftarans', 'tanggal')); 
    }

    // ==========================================
    // 🔥 SISTEM PEMANGGILAN & KEHADIRAN 🔥
    // ==========================================

    // 1. Panggil Pasien (Bunyikan Alarm)
    public function panggilPasien(Request $request, $id)
    {
        try {
            $pendaftaran = Pendaftaran::findOrFail($id);
            $pendaftaran->increment('jumlah_panggilan');
            $pendaftaran->status_panggilan = 'dipanggil';
            $pendaftaran->save();

            return response()->json([
                'success' => true, 
                'message' => 'Pasien sedang dipanggil.',
                'jumlah_panggilan' => $pendaftaran->jumlah_panggilan
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 2. Tandai Hadir (Kunci alur: Membuka tombol Input Data)
    public function tandaiHadir(Request $request, $id)
    {
        try {
            $pendaftaran = Pendaftaran::findOrFail($id);
            
            // Matikan status panggilan (biar bersih)
            $pendaftaran->status_panggilan = 'hadir'; 
            
            // Ubah Status Utama jadi 'Hadir'
            // Inilah trigger agar tombol "Input Data" di view terbuka
            $pendaftaran->status = 'Hadir'; 
            
            $pendaftaran->save();

            return response()->json([
                'success' => true, 
                'message' => 'Pasien dikonfirmasi hadir. Menu input data dibuka.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Stop Panggil Manual (Opsional, jika admin salah panggil tapi pasien belum datang)
    public function stopPanggil(Request $request, $id)
    {
        try {
            $pendaftaran = Pendaftaran::findOrFail($id);
            $pendaftaran->status_panggilan = 'menunggu'; 
            $pendaftaran->save();

            return response()->json([
                'success' => true, 
                'message' => 'Panggilan dihentikan sementara.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 4. Alihkan / Skip Pasien
    public function alihkanPasien(Request $request, $id)
    {
        try {
            $pendaftaran = Pendaftaran::findOrFail($id);
            $pendaftaran->status_panggilan = 'dialihkan';
            $pendaftaran->save();

            return response()->json([
                'success' => true, 
                'message' => 'Pasien dialihkan ke antrian belakang.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==========================================
    // LAPORAN & RIWAYAT
    // ==========================================

    public function laporan(Request $request)
    {
        $filter = $request->input('filter', 'bulan_ini'); 
        $tanggalDipilih = $request->input('tanggal', Carbon::today()->toDateString());
        $bulanDipilih = $request->input('bulan', Carbon::now()->format('Y-m')); 

        $kunjunganHariIni = Pendaftaran::whereDate('created_at', Carbon::today())->count();
        $kunjunganBulanIni = Pendaftaran::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        $semuaKunjungan = Pendaftaran::count();

        $query = Pendaftaran::join('users', 'pendaftarans.user_id', '=', 'users.id')
            ->leftJoin('jadwal_prakteks', 'pendaftarans.jadwal_praktek_id', '=', 'jadwal_prakteks.id')
            ->leftJoin('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
            ->select(
                'pendaftarans.id as pendaftaran_id',
                'pendaftarans.created_at as tanggal_kunjungan',
                'pendaftarans.nama_layanan as layanan',
                'users.id as pasien_id',
                'users.name as nama_pasien',
                'users.alamat',
                'users.tanggal_lahir',
                'tenaga_medis.name as nama_dokter',
                'users.profile_photo_path'
            );

        if ($filter == 'hari_ini') {
            $query->whereDate('pendaftarans.created_at', Carbon::today());
        } elseif ($filter == 'bulan_ini') {
            $query->whereMonth('pendaftarans.created_at', Carbon::now()->month)->whereYear('pendaftarans.created_at', Carbon::now()->year);
        } elseif ($filter == 'tanggal') {
            $query->whereDate('pendaftarans.created_at', $tanggalDipilih);
        } elseif ($filter == 'bulan_terpilih') {
            $carbonBulan = Carbon::parse($bulanDipilih);
            $query->whereMonth('pendaftarans.created_at', $carbonBulan->month)->whereYear('pendaftarans.created_at', $carbonBulan->year);
        }

        $kunjunganData = $query->latest('pendaftarans.created_at')->get();

        $chartLabels = [];
        $chartData = [];

        if ($filter == 'hari_ini' || $filter == 'tanggal') {
            $tanggal = ($filter == 'hari_ini') ? Carbon::today() : $tanggalDipilih;
            $chartQuery = Pendaftaran::select(DB::raw('HOUR(created_at) as jam'), DB::raw('COUNT(*) as jumlah'))
                ->whereDate('created_at', $tanggal)->groupBy('jam')->orderBy('jam', 'asc')->get();
            $chartLabels = $chartQuery->pluck('jam')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');
        } else {
            $chartQuery = Pendaftaran::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->whereMonth('created_at', Carbon::now()->month)->groupBy('tanggal')->orderBy('tanggal', 'asc')->get();
            $chartLabels = $chartQuery->pluck('tanggal')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');
        }
        
        return view('admin.laporan', compact('kunjunganHariIni', 'kunjunganBulanIni', 'semuaKunjungan', 'kunjunganData', 'chartLabels', 'chartData', 'filter', 'tanggalDipilih', 'bulanDipilih'));
    }

    public function riwayatPasien(Request $request, User $user)
    {
        $query = Pemeriksaan::with(['pendaftaran.user', 'pendaftaran.pemeriksaanAwal', 'tenagaMedis', 'resep'])
            ->where('pasien_id', $user->id);

        if ($request->filled('tanggal')) { $query->whereDate('pemeriksaans.created_at', $request->tanggal); }
        if ($request->filled('layanan_id')) { $query->whereHas('pendaftaran', function($q) use ($request) { $q->where('layanan_id', $request->layanan_id); }); }
        if ($request->filled('tenaga_medis_id')) { $query->where('tenaga_medis_id', $request->tenaga_medis_id); }

        $riwayats = $query->latest('pemeriksaans.created_at')->get();
        $layanans = Layanan::orderBy('nama_layanan')->get();
        $tenagaMedisList = TenagaMedis::orderBy('name')->get();
        
        return view('admin.riwayat.index', compact('riwayats', 'layanans', 'tenagaMedisList', 'request', 'user'));
    }
}
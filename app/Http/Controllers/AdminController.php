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
use App\Models\DoctorAvailability; // 🔥 Pastikan Model ini di-import
use Illuminate\Support\Facades\DB;
use App\Models\Layanan;
use Illuminate\Support\Facades\Route;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function dashboard()
    {
        // 1. Ambil data Admin yang sedang login
        $user = Auth::guard('admin')->user();

        // 2. Setup Tanggal & Locale
        $today = Carbon::today();
        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');

        // 🔥 LOGIKA BARU: Cari Dokter yang TUTUP/LIBUR Hari Ini
        // Kita cari di tabel doctor_availabilities siapa saja yang is_available = 0 pada tanggal hari ini
        $unavailableDoctorIds = DoctorAvailability::whereDate('date', $today)
            ->where('is_available', false)
            ->pluck('tenaga_medis_id')
            ->toArray();

        // 3. HITUNG STATISTIK (KPI)
        $jumlahPasienHariIni = Pendaftaran::whereDate('jadwal_dipilih', $today)->count();
        
        $jumlahMenunggu = Pendaftaran::whereDate('jadwal_dipilih', $today)
                                     ->where('status', 'Menunggu')
                                     ->count();
                                     
        $jumlahSelesai = Pendaftaran::whereDate('jadwal_dipilih', $today)
                                    ->where('status', 'Selesai')
                                    ->count();
                                    
        $jumlahTenagaMedis = TenagaMedis::count();
        
        $jumlahTotalPasien = User::where('role', 'pasien')
                                 ->orWhereNull('role')
                                 ->count();

        // 🔥 PERBAIKAN: Hitung Dokter Aktif (Kecuali yang Libur/Batal Hari Ini)
        $jumlahDokterAktifHariIni = JadwalPraktek::whereJsonContains('hari', $namaHariIni)
                                                ->whereNotIn('tenaga_medis_id', $unavailableDoctorIds) // Filter Dokter Libur
                                                ->distinct('tenaga_medis_id')
                                                ->count('tenaga_medis_id');
        
        // Menghitung total pemeriksaan tahun ini (agar tidak error undefined variable)
        $pemeriksaanTahunIni = Pemeriksaan::whereYear('created_at', Carbon::now()->year)->count();


        // 4. AMBIL DATA UNTUK TABEL
        // 🔥 PERBAIKAN: Jangan tampilkan jadwal dokter yang libur hari ini di tabel
        $jadwalHariIni = JadwalPraktek::with('tenagaMedis')
                                      ->whereJsonContains('hari', $namaHariIni)
                                      ->whereNotIn('tenaga_medis_id', $unavailableDoctorIds) // Filter Dokter Libur
                                      ->get();
                                      
        $pendaftaranMenunggu = Pendaftaran::with('user')
                                          ->whereDate('jadwal_dipilih', $today)
                                          ->where('status', 'Menunggu')
                                          ->orderBy('no_antrian', 'asc')
                                          ->take(5)
                                          ->get();

        // 5. KIRIM SEMUA DATA KE VIEW
        return view('admin.dashboard', compact(
            'user',
            'jumlahPasienHariIni',
            'jumlahMenunggu',
            'jumlahSelesai',
            'jumlahTenagaMedis',
            'jumlahTotalPasien',
            'jumlahDokterAktifHariIni',
            'pemeriksaanTahunIni', // Sudah didefinisikan di atas
            'jadwalHariIni',
            'pendaftaranMenunggu'
        ));
    }

    // ... (method-method Anda yang lain tidak berubah) ...

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
            $q->where('role', 'pasien')
              ->orWhereNull('role'); 
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $pasiens = $query->orderBy('created_at', 'desc')->paginate(8);

        return view('admin.keloladatapasien', [
            'pasiens' => $pasiens,
            'search' => $search,
        ]);
    }

    public function catatanpemeriksaan(Request $request)
    {
        $tanggal = $request->input('tanggal'); 

        $query = Pendaftaran::with(['user', 'jadwalPraktek.tenagaMedis']);

        if ($tanggal) {
            $query->whereDate('jadwal_dipilih', $tanggal);
        }

        $pendaftarans = $query->orderByRaw("FIELD(status, 'Menunggu', 'Diperiksa Awal', 'Selesai')")
                             ->orderBy('no_antrian', 'asc')
                             ->get()
                             ->groupBy('nama_layanan');

        return view('admin.catatanpemeriksaan', compact('pendaftarans', 'tanggal')); 
    }

    public function laporan(Request $request)
    {
        $filter = $request->input('filter', 'bulan_ini'); 
        $tanggalDipilih = $request->input('tanggal', Carbon::today()->toDateString());
        $bulanDipilih = $request->input('bulan', Carbon::now()->format('Y-m')); 

        // KPI
        $kunjunganHariIni = Pendaftaran::whereDate('created_at', Carbon::today())->count();
        $kunjunganBulanIni = Pendaftaran::whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count();
        $semuaKunjungan = Pendaftaran::count();

        // Query Tabel
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
            $query->whereMonth('pendaftarans.created_at', Carbon::now()->month)
                  ->whereYear('pendaftarans.created_at', Carbon::now()->year);
        } elseif ($filter == 'tanggal') {
            $query->whereDate('pendaftarans.created_at', $tanggalDipilih);
        } elseif ($filter == 'bulan_terpilih') {
            $carbonBulan = Carbon::parse($bulanDipilih);
            $query->whereMonth('pendaftarans.created_at', $carbonBulan->month)
                  ->whereYear('pendaftarans.created_at', $carbonBulan->year);
        }

        $kunjunganData = $query->latest('pendaftarans.created_at')->get();

        // Data Grafik
        $chartLabels = [];
        $chartData = [];

        if ($filter == 'hari_ini' || $filter == 'tanggal') {
            $tanggal = ($filter == 'hari_ini') ? Carbon::today() : $tanggalDipilih;
            $chartQuery = Pendaftaran::select(DB::raw('HOUR(created_at) as jam'), DB::raw('COUNT(*) as jumlah'))
                                     ->whereDate('created_at', $tanggal)
                                     ->groupBy('jam')
                                     ->orderBy('jam', 'asc')
                                     ->get();
            $chartLabels = $chartQuery->pluck('jam')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_ini' || $filter == 'bulan_terpilih') {
            $carbonBulan = ($filter == 'bulan_ini') ? Carbon::now() : Carbon::parse($bulanDipilih);
            
            $chartQuery = Pendaftaran::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                                     ->whereMonth('created_at', $carbonBulan->month)
                                     ->whereYear('created_at', $carbonBulan->year)
                                     ->groupBy('tanggal')
                                     ->orderBy('tanggal', 'asc')
                                     ->get();
            
            $chartLabels = $chartQuery->pluck('tanggal')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } else {
            $chartQuery = Pendaftaran::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'), DB::raw('COUNT(*) as jumlah'))
                                     ->groupBy('bulan')
                                     ->orderBy('bulan', 'asc')
                                     ->get();
            $chartLabels = $chartQuery->pluck('bulan')->map(fn($bln) => Carbon::parse($bln)->isoFormat('MMM YYYY'));
            $chartData = $chartQuery->pluck('jumlah');
        }
        
        return view('admin.laporan', compact(
            'kunjunganHariIni',
            'kunjunganBulanIni',
            'semuaKunjungan',
            'kunjunganData',
            'chartLabels',
            'chartData',
            'filter',
            'tanggalDipilih',
            'bulanDipilih'
        ));
    }

    public function riwayatPasien(Request $request, User $user)
    {
        $query = Pemeriksaan::with([
            'pendaftaran.user', 
            'pendaftaran.pemeriksaanAwal', 
            'tenagaMedis',
            'resep'
        ])->where('pasien_id', $user->id);

        if ($request->filled('tanggal')) {
            $query->whereDate('pemeriksaans.created_at', $request->tanggal);
        }

        if ($request->filled('layanan_id')) {
            $query->whereHas('pendaftaran', function($q) use ($request) {
                $q->where('layanan_id', $request->layanan_id);
            });
        }

        if ($request->filled('tenaga_medis_id')) {
            $query->where('tenaga_medis_id', $request->tenaga_medis_id);
        }

        $riwayats = $query->latest('pemeriksaans.created_at')->get();

        $layanans = Layanan::orderBy('nama_layanan')->get();
        $tenagaMedisList = TenagaMedis::orderBy('name')->get();
        
        return view('admin.riwayat.index', [
            'riwayats' => $riwayats,
            'layanans' => $layanans,
            'tenagaMedisList' => $tenagaMedisList,
            'request' => $request, 
            'pasien' => $user, 
        ]);
    }
}

// Route API di bawah class ini sebaiknya dipindah ke routes/api.php atau routes/web.php
// Tapi jika ingin tetap disini (bad practice tapi jalan), pastikan di luar class.

// --- API NOTIFIKASI / HISTORI PENDAFTARAN ---
// Catatan: Sebaiknya route ini dipindahkan ke file `routes/api.php` atau `routes/web.php`
Route::get('/pendaftaran/history', function (Request $request) {
    $userId = $request->query('user_id'); // Ambil ID User dari parameter

    $pendaftarans = \App\Models\Pendaftaran::with(['jadwalPraktek.tenagaMedis'])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'no_antrian' => $item->no_antrian,
                'nama_layanan' => $item->nama_layanan,
                'dokter_name' => $item->jadwalPraktek->tenagaMedis->name ?? 'N/A',
                'jadwal_dipilih' => $item->jadwal_dipilih, // Format Y-m-d
                'status' => $item->no_antrian ? 'Terdaftar' : 'Menunggu'
            ];
        });

    return response()->json([
        'status' => 'success',
        'data' => $pendaftarans
    ]);
});
<?php

namespace App\Http\Controllers; // <-- PASTIKAN INI BENAR

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\JadwalPraktek;
use App\Models\User;
use App\Models\Pemeriksaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini

// Pastikan extends Controller
class TenagaMedisController extends Controller 
{
    /**
     * Menampilkan dashboard khusus untuk tenaga medis.
     */
   public function dashboard()
{
    // --- 1. AMBIL DATA DASAR ---
    $tenagaMedis = Auth::guard('tenaga_medis')->user();
    $tenagaMedisId = $tenagaMedis->id;
    $today = Carbon::today();
    Carbon::setLocale('id');
    $namaHariIni = Carbon::now()->translatedFormat('l');

    // --- 2. AMBIL LAYANAN YANG DITANGANI ---
    $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                     ->distinct()
                                     ->pluck('layanan')
                                     ->toArray();

    // --- 3. DATA UNTUK KARTU KPI (RINGKASAN) ---
    // Total pasien hari ini untuk dokter ini
    $jumlahTotalPasien = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                                    ->whereDate('jadwal_dipilih', $today)
                                    ->count();
    
    // Jumlah pasien yang sudah diperiksa (sudah ada di tabel pemeriksaans)
    $jumlahSelesai = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                ->whereDate('created_at', $today)
                                ->count();
    
    // Jumlah pasien yang masih menunggu (Total - Selesai)
    $jumlahMenunggu = $jumlahTotalPasien - $jumlahSelesai;


    // --- 4. DATA JADWAL PRAKTEK DOKTER HARI INI ---
    $jadwalHariIni = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                  ->whereJsonContains('hari', $namaHariIni)
                                  ->first();

    // --- 5. DATA TABEL ANTRIAN (PASIEN SELANJUTNYA) ---
    $pendaftaranMenunggu = Pendaftaran::with('user')
                                      ->whereIn('nama_layanan', $layanansDitangani)
                                      ->whereDate('jadwal_dipilih', $today)
                                      ->where('status', '!=', 'Selesai') // Ambil yang Menunggu atau Diperiksa Awal
                                      ->orderBy('no_antrian', 'asc') // Urutkan berdasarkan no antrian
                                      ->take(5) // Ambil 5 antrian teratas
                                      ->get();

    // --- 6. KIRIM SEMUA DATA KE VIEW ---
    return view('tenaga_medis.dashboard', compact(
        'tenagaMedis',
        'jumlahTotalPasien',
        'jumlahSelesai',
        'jumlahMenunggu',
        'jadwalHariIni',
        'pendaftaranMenunggu'
    ));
}
    /**
     * Menampilkan form login untuk tenaga medis.
     */
    public function showLoginForm()
    {
        return view('tenaga_medis.login');
    }

    /**
     * Memproses percobaan login dari form tenaga medis.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::guard('tenaga_medis')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('tenaga-medis.dashboard');
        }

        return back()->withErrors(['email' => 'Login gagal, email atau password salah.'])->onlyInput('email');
    }

    /**
     * Memproses logout untuk tenaga medis.
     */
    public function logout(Request $request)
    {
        Auth::guard('tenaga_medis')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenaga-medis.login');
    }

    /**
     * Menampilkan daftar pasien yang telah mendaftar
     * HANYA untuk layanan yang ditangani oleh tenaga medis ini.
     */
  public function lihatPasien(Request $request)
{
    $tenagaMedisId = Auth::guard('tenaga_medis')->id();
    if (!$tenagaMedisId) {
        return redirect()->route('tenaga-medis.login')->withErrors(['email' => 'Sesi tidak valid.']);
    }

    // Ambil tanggal dari URL. Sekarang default-nya null
    $tanggal = $request->input('tanggal'); // Hapus default Carbon::today()

    $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                     ->distinct()
                                     ->pluck('layanan')
                                     ->toArray();

    if (empty($layanansDitangani)) {
         $pendaftarans = collect();
    } else {
        $query = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                           ->with('user')
                           ->latest();

        // HANYA filter jika $tanggal ADA ISINYA
        if ($tanggal) {
            $query->whereDate('jadwal_dipilih', $tanggal);
        } else {
            // Jika tidak ada filter tanggal, tampilkan semua yang statusnya BUKAN Selesai
            $query->where('status', '!=', 'Selesai');
        }

        $pendaftarans = $query->get();
    }

    // Kirim $tanggal (bisa jadi null) ke view
    return view('tenaga_medis.pasien.index', compact('pendaftarans', 'tanggal'));
}

     /**
      * Menampilkan detail pendaftaran pasien.
      */
     public function detailPasien(Pendaftaran $pendaftaran)
     {
         return view('tenaga_medis.pasien.show', compact('pendaftaran'));
     }

    /**
     * Menampilkan riwayat pemeriksaan seorang pasien.
     */
    public function riwayatPasien(User $user) 
    {
        if ($user->role !== 'pasien') {
            abort(404);
        }

        $riwayats = Pemeriksaan::where('pasien_id', $user->id)
                               ->with(['tenagaMedis', 'pendaftaran.pemeriksaanAwal'])
                               ->latest('created_at')
                               ->get();

        return view('tenaga_medis.pasien.riwayat', compact('user', 'riwayats'));
    }

    /**
     * Menampilkan riwayat pemeriksaan yang dilakukan oleh tenaga medis yang sedang login.
     */
    public function myPemeriksaanHistory(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();

        // Ambil filter dari request
        $tanggalFilter = $request->input('tanggal');
        $namaFilter = $request->input('nama');

        $query = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                            ->with(['pasien', 'pendaftaran.pemeriksaanAwal'])
                            ->latest('created_at');

        // Terapkan filter tanggal jika ada
        if ($tanggalFilter) {
            $query->whereDate('created_at', $tanggalFilter);
        }

        // Terapkan filter nama pasien jika ada
        if ($namaFilter) {
            $query->whereHas('pasien', function ($q) use ($namaFilter) {
                $q->where('name', 'like', '%' . $namaFilter . '%');
            });
        }

        $riwayats = $query->get();
        return view('tenaga_medis.riwayat_pemeriksaan_saya', compact('riwayats', 'tanggalFilter', 'namaFilter'));
    }

    /**
     * Menampilkan halaman laporan untuk tenaga medis yang sedang login.
     */
    public function laporan(Request $request)
    {
        // --- 1. AMBIL DATA DASAR & FILTER ---
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        $filter = $request->input('filter', 'bulan_ini'); 
        $tanggalDipilih = $request->input('tanggal', Carbon::today()->toDateString());
        $bulanDipilih = $request->input('bulan', Carbon::now()->format('Y-m')); 

        // --- 2. DATA UNTUK KARTU KPI (KHUSUS TENAGA MEDIS INI) ---
        $kunjunganHariIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                       ->whereDate('created_at', Carbon::today())->count();
        $kunjunganBulanIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count();
        $semuaKunjungan = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)->count();

        // --- 3. DATA UNTUK TABEL (SESUAI FILTER & TENAGA MEDIS) ---
        $query = Pemeriksaan::where('pemeriksaans.tenaga_medis_id', $tenagaMedisId)
                            ->join('pendaftarans', 'pemeriksaans.pendaftaran_id', '=', 'pendaftarans.id')
                            ->join('users', 'pemeriksaans.pasien_id', '=', 'users.id')
                            ->select(
                                'pemeriksaans.created_at as tanggal_kunjungan',
                                'pendaftarans.nama_layanan as layanan',
                                'users.id as pasien_id',
                                'users.name as nama_pasien',
                                'users.profile_photo_path'
                            );

        // Terapkan filter waktu
        if ($filter == 'hari_ini') {
            $query->whereDate('pemeriksaans.created_at', Carbon::today());
        } elseif ($filter == 'bulan_ini') {
            $query->whereMonth('pemeriksaans.created_at', Carbon::now()->month)
                  ->whereYear('pemeriksaans.created_at', Carbon::now()->year);
        } elseif ($filter == 'tanggal') {
            $query->whereDate('pemeriksaans.created_at', $tanggalDipilih);
        } elseif ($filter == 'bulan_terpilih') {
            $carbonBulan = Carbon::parse($bulanDipilih);
            $query->whereMonth('pemeriksaans.created_at', $carbonBulan->month)
                  ->whereYear('pemeriksaans.created_at', $carbonBulan->year);
        }

        $kunjunganData = $query->latest('pemeriksaans.created_at')->get();

        // --- 4. DATA UNTUK GRAFIK (SESUAI FILTER & TENAGA MEDIS) ---
        $chartLabels = [];
        $chartData = [];
        $baseChartQuery = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId);

        if ($filter == 'hari_ini' || $filter == 'tanggal') {
            $tanggal = ($filter == 'hari_ini') ? Carbon::today() : $tanggalDipilih;
            $chartQuery = $baseChartQuery->select(DB::raw('HOUR(created_at) as jam'), DB::raw('COUNT(*) as jumlah'))
                                     ->whereDate('created_at', $tanggal)
                                     ->groupBy('jam')->orderBy('jam', 'asc')->get();
            $chartLabels = $chartQuery->pluck('jam')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_ini' || $filter == 'bulan_terpilih') {
            $carbonBulan = ($filter == 'bulan_ini') ? Carbon::now() : Carbon::parse($bulanDipilih);
            $chartQuery = $baseChartQuery->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                                     ->whereMonth('created_at', $carbonBulan->month)
                                     ->whereYear('created_at', $carbonBulan->year)
                                     ->groupBy('tanggal')->orderBy('tanggal', 'asc')->get();
            $chartLabels = $chartQuery->pluck('tanggal')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } else { // 'semua_data'
            $chartQuery = $baseChartQuery->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'), DB::raw('COUNT(*) as jumlah'))
                                     ->groupBy('bulan')->orderBy('bulan', 'asc')->get();
            $chartLabels = $chartQuery->pluck('bulan')->map(fn($bln) => Carbon::parse($bln)->isoFormat('MMM YYYY'));
            $chartData = $chartQuery->pluck('jumlah');
        }

        // 5. Kirim semua data ke view baru
        return view('tenaga_medis.laporan', compact(
            'kunjunganHariIni', 'kunjunganBulanIni', 'semuaKunjungan',
            'kunjunganData', 'chartLabels', 'chartData',
            'filter', 'tanggalDipilih', 'bulanDipilih'
        ));
    }
} 

<?php

namespace App\Http\Controllers; // Pastikan namespace ini benar

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Pendaftaran;
use App\Models\JadwalPraktek;
use App\Models\Admin;
use App\Models\User;
use App\Models\Pemeriksaan;
use App\Models\TenagaMedis; // Pastikan ini di-import
use Illuminate\Support\Facades\DB;
use App\Models\Layanan;


class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function dashboard()
{
    // --- 1. AMBIL DATA STATISTIK (KPI) ---
    $today = Carbon::today();
    Carbon::setLocale('id');
    $namaHariIni = Carbon::now()->translatedFormat('l');

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

    // --- TAMBAHAN BARU: Hitung Dokter Aktif Hari Ini ---
    $jumlahDokterAktifHariIni = JadwalPraktek::whereJsonContains('hari', $namaHariIni)
                                            ->distinct('tenaga_medis_id') // Hitung tenaga_medis_id unik
                                            ->count('tenaga_medis_id');
    // --- AKHIR TAMBAHAN ---


    // --- 2. AMBIL DATA UNTUK TABEL ---
    $jadwalHariIni = JadwalPraktek::with('tenagaMedis')
                                  ->whereJsonContains('hari', $namaHariIni)
                                  ->get();
    $pendaftaranMenunggu = Pendaftaran::with('user')
                                      ->whereDate('jadwal_dipilih', $today)
                                      ->where('status', 'Menunggu')
                                      ->orderBy('no_antrian', 'asc') // Urutkan berdasarkan nomor antrian
                                      ->take(5)
                                      ->get();

    // --- 3. KIRIM SEMUA DATA KE VIEW ---
    return view('admin.dashboard', compact(
        'jumlahPasienHariIni',
        'jumlahMenunggu',
        'jumlahSelesai',
        'jumlahTenagaMedis',
        'jumlahTotalPasien',
        'jumlahDokterAktifHariIni', // <-- Variabel baru ditambahkan
        'jadwalHariIni',
        'pendaftaranMenunggu'
    ));
}
    // ... (method-method Anda yang lain) ...

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
          ->orWhereNull('role'); // Izinkan user lama
    });

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    // ✅ Pagination supaya tidak berat jika data banyak
    $pasiens = $query->orderBy('created_at', 'desc')->paginate(8);

    return view('admin.keloladatapasien', [
        'pasiens' => $pasiens,
        'search' => $search,
    ]);
}

 public function catatanpemeriksaan(Request $request)
{
    // Ambil tanggal dari URL. Sekarang default-nya null
    $tanggal = $request->input('tanggal'); // Hapus default Carbon::today()

    $query = Pendaftaran::with(['user', 'jadwalPraktek.tenagaMedis']);

    // HANYA filter jika $tanggal ADA ISINYA
    if ($tanggal) {
        $query->whereDate('jadwal_dipilih', $tanggal);
    }

    // Urutkan berdasarkan status (Menunggu, Diperiksa Awal, Selesai) dan kemudian nomor antrian
    $pendaftarans = $query->orderByRaw("FIELD(status, 'Menunggu', 'Diperiksa Awal', 'Selesai')")
                         ->orderBy('no_antrian', 'asc')
                         ->get()
                         ->groupBy('nama_layanan');

    // Kirim $tanggal (bisa jadi null) ke view
    return view('admin.catatanpemeriksaan', compact('pendaftarans', 'tanggal')); 
}
 public function laporan(Request $request)
{
    // --- 1. AMBIL FILTER ---
    $filter = $request->input('filter', 'bulan_ini'); 
    $tanggalDipilih = $request->input('tanggal', Carbon::today()->toDateString());
    // Ambil bulan terpilih, default ke bulan & tahun saat ini
    $bulanDipilih = $request->input('bulan', Carbon::now()->format('Y-m')); 

    // --- 2. DATA UNTUK KARTU KPI ---
    $kunjunganHariIni = Pendaftaran::whereDate('created_at', Carbon::today())->count();
    $kunjunganBulanIni = Pendaftaran::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();
    $semuaKunjungan = Pendaftaran::count();

    // --- 3. DATA UNTUK TABEL (SESUAI FILTER) ---
    $query = Pendaftaran::join('users', 'pendaftarans.user_id', '=', 'users.id')
                        ->leftJoin('jadwal_prakteks', 'pendaftarans.jadwal_praktek_id', '=', 'jadwal_prakteks.id')
                        ->leftJoin('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id') // Pastikan relasi dimuat
                        ->select(
                            'pendaftarans.id as pendaftaran_id',
                            'pendaftarans.created_at as tanggal_kunjungan',
                            'pendaftarans.nama_layanan as layanan',
                            'users.id as pasien_id',
                            'users.name as nama_pasien',
                            'users.alamat',
                            'users.tanggal_lahir',
                            'tenaga_medis.name as nama_dokter',
                            'users.profile_photo_path' // <-- TAMBAHKAN INI
                        );

    // Terapkan filter
    if ($filter == 'hari_ini') {
        $query->whereDate('pendaftarans.created_at', Carbon::today());
    } elseif ($filter == 'bulan_ini') {
        $query->whereMonth('pendaftarans.created_at', Carbon::now()->month)
              ->whereYear('pendaftarans.created_at', Carbon::now()->year);
    } elseif ($filter == 'tanggal') {
        $query->whereDate('pendaftarans.created_at', $tanggalDipilih);
    } 
    // --- TAMBAHAN FILTER BARU UNTUK BULAN DIPILIH ---
    elseif ($filter == 'bulan_terpilih') {
        $carbonBulan = Carbon::parse($bulanDipilih); // Ubah "2025-10" jadi objek Carbon
        $query->whereMonth('pendaftarans.created_at', $carbonBulan->month)
              ->whereYear('pendaftarans.created_at', $carbonBulan->year);
    }
    // Jika 'semua_data', tidak perlu 'where' tambahan

    $kunjunganData = $query->latest('pendaftarans.created_at')->get();

    // --- 4. DATA UNTUK GRAFIK (SESUAI FILTER) ---
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

    // --- MODIFIKASI: Gabungkan logika 'bulan_ini' dan 'bulan_terpilih' ---
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

    } else { // 'semua_data'
        $chartQuery = Pendaftaran::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'), DB::raw('COUNT(*) as jumlah'))
                                 ->groupBy('bulan')
                                 ->orderBy('bulan', 'asc')
                                 ->get();
        $chartLabels = $chartQuery->pluck('bulan')->map(fn($bln) => Carbon::parse($bln)->isoFormat('MMM YYYY'));
        $chartData = $chartQuery->pluck('jumlah');
    }
    

    // 5. Kirim semua data ke view
    return view('admin.laporan', compact(
        'kunjunganHariIni',
        'kunjunganBulanIni',
        'semuaKunjungan',
        'kunjunganData',
        'chartLabels',
        'chartData',
        'filter',
        'tanggalDipilih',
        'bulanDipilih' // <-- Kirim bulan yang dipilih ke view
    ));
}

   public function riwayatPasien(Request $request, User $user) // $user otomatis diambil dari route model binding
{
    // 1. Mulai query HANYA untuk pasien ini
    $query = Pemeriksaan::with([
        'pendaftaran.user', 
        'pendaftaran.pemeriksaanAwal', 
        'tenagaMedis',
        'resep' // Pastikan relasi resep ada di model Pemeriksaan
    ])->where('pasien_id', $user->id);

    // 2. Terapkan Filter Tambahan (tanggal, layanan, nakes)
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

    // 3. Ambil data hasil filter
    $riwayats = $query->latest('pemeriksaans.created_at')->get();

    // 4. Ambil data untuk dropdown filter
    $layanans = Layanan::orderBy('nama_layanan')->get();
    $tenagaMedisList = TenagaMedis::orderBy('name')->get();
    
    // 5. Kirim ke View Admin yang baru
    return view('admin.riwayat.index', [
        'riwayats' => $riwayats,
        'layanans' => $layanans,
        'tenagaMedisList' => $tenagaMedisList,
        'request' => $request, 
        'pasien' => $user, // Mengirim data pasien ($user) ke view
    ]);
}}
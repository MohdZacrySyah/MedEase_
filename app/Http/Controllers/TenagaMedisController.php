<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\JadwalPraktek;
use App\Models\User;
use App\Models\Pemeriksaan;
use App\Models\Message; // 🔥 PASTIKAN MODEL MESSAGE DIIMPORT
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class TenagaMedisController extends Controller 
{
    /**
     * 🔥 METHOD AJAX UNTUK LAPORAN
     * Mengembalikan data dalam format JSON untuk dirender oleh JavaScript di client-side.
     */
    public function getLaporanDataJson(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        $filter = $request->input('filter', 'hari_ini'); 
        $tanggalDipilih = $request->input('tanggal');
        $bulanDipilih = $request->input('bulan'); // Format: YYYY-MM

        // --- 1. DATA KPI (STATISTIK) ---
        $kunjunganHariIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                       ->whereDate('created_at', Carbon::today())->count();
                                       
        $kunjunganBulanIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count();
                                        
        $semuaKunjungan = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)->count();

        // --- 2. QUERY DASAR UNTUK DATA ---
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

        $chartQueryBase = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId);

        // --- 3. FILTER LOGIC ---
        if ($filter == 'hari_ini') {
            // Table Filter
            $query->whereDate('pemeriksaans.created_at', Carbon::today());
            
            // Chart Filter (Per Jam)
            $chartQuery = $chartQueryBase->select(DB::raw('HOUR(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereDate('created_at', Carbon::today())
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
            
            $chartLabels = $chartQuery->pluck('label')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_ini') {
            // Table Filter
            $query->whereMonth('pemeriksaans.created_at', Carbon::now()->month)
                  ->whereYear('pemeriksaans.created_at', Carbon::now()->year);
            
            // Chart Filter (Per Hari)
            $chartQuery = $chartQueryBase->select(DB::raw('DATE(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereMonth('created_at', Carbon::now()->month)
                                         ->whereYear('created_at', Carbon::now()->year)
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'tanggal' && $tanggalDipilih) {
            // Table Filter
            $query->whereDate('pemeriksaans.created_at', $tanggalDipilih);
            
            // Chart Filter (Per Jam)
            $chartQuery = $chartQueryBase->select(DB::raw('HOUR(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereDate('created_at', $tanggalDipilih)
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_terpilih' && $bulanDipilih) {
            $carbonBulan = Carbon::parse($bulanDipilih); // Format Y-m
            
            // Table Filter
            $query->whereMonth('pemeriksaans.created_at', $carbonBulan->month)
                  ->whereYear('pemeriksaans.created_at', $carbonBulan->year);
            
            // Chart Filter (Per Hari)
            $chartQuery = $chartQueryBase->select(DB::raw('DATE(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereMonth('created_at', $carbonBulan->month)
                                         ->whereYear('created_at', $carbonBulan->year)
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } else { // 'semua_data'
            // Chart Filter (Per Bulan)
            $chartQuery = $chartQueryBase->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($bln) => Carbon::parse($bln)->isoFormat('MMM YYYY'));
            $chartData = $chartQuery->pluck('jumlah');
        }

        $kunjunganData = $query->latest('pemeriksaans.created_at')->get();

        // --- 4. FORMAT DATA JSON UNTUK TABEL (PENTING!) ---
        // Kita ubah data collection menjadi array bersih yang siap dibaca oleh JS di frontend
        $tableData = $kunjunganData->map(function($item) {
            return [
                'pasien_id' => $item->pasien_id,
                'nama_pasien' => $item->nama_pasien,
                'layanan' => $item->layanan,
                'tanggal_formatted' => Carbon::parse($item->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm'),
                // Generate URL lengkap untuk foto profil atau null jika tidak ada
                'profile_photo_url' => $item->profile_photo_path ? asset('storage/' . $item->profile_photo_path) : null,
            ];
        });

        // --- 5. RETURN JSON RESPONSE ---
        return response()->json([
            'stats' => [
                'hari_ini' => $kunjunganHariIni,
                'bulan_ini' => $kunjunganBulanIni,
                'total' => $semuaKunjungan
            ],
            // 'table_data' ini yang akan dipakai oleh fungsi updateTable(data.table_data) di JS
            'table_data' => $tableData, 
            'table_count' => $kunjunganData->count(),
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData,
                'title' => ucfirst(str_replace('_', ' ', $filter)) . ($tanggalDipilih ? " ($tanggalDipilih)" : "")
            ]
        ]);
    }

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

    public function getDashboardData()
    {
        $tenagaMedis = Auth::guard('tenaga_medis')->user();
        $tenagaMedisId = $tenagaMedis->id;
        $today = Carbon::today();
        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');

        // 1. Ambil Layanan
        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        // 2. Hitung Statistik
        $jumlahTotalPasien = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                                        ->whereDate('jadwal_dipilih', $today)
                                        ->count();
        
        $jumlahSelesai = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                    ->whereDate('created_at', $today)
                                    ->count();
        
        $jumlahMenunggu = $jumlahTotalPasien - $jumlahSelesai;

        // 3. Ambil Antrian (Sama seperti dashboard utama)
        $pendaftaranMenunggu = Pendaftaran::with('user')
                                        ->whereIn('nama_layanan', $layanansDitangani)
                                        ->whereDate('jadwal_dipilih', $today)
                                        ->where('status', '!=', 'Selesai')
                                        ->orderBy('no_antrian', 'asc')
                                        ->take(5)
                                        ->get();

        // 4. Render HTML tabel antrian dari partial view
        $tableHtml = view('tenaga_medis.components.table_antrian', compact('pendaftaranMenunggu'))->render();

        // 5. Return JSON
        return response()->json([
            'total_pasien' => $jumlahTotalPasien,
            'menunggu' => $jumlahMenunggu,
            'selesai' => $jumlahSelesai,
            'antrian_count' => $pendaftaranMenunggu->count(),
            'table_html' => $tableHtml
        ]);
    }

    public function getPasienData(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        if (!$tenagaMedisId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $tanggal = $request->input('tanggal'); 

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        if (empty($layanansDitangani)) {
            $pendaftarans = collect();
        } else {
            // [PERBAIKAN] Pastikan ->latest() SUDAH DIHAPUS agar tidak mengurutkan berdasarkan waktu input
            $query = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                            ->with('user');

            // Terapkan filter yang sama persis dengan halaman utama
            if ($tanggal) {
                $query->whereDate('jadwal_dipilih', $tanggal);
            } else {
                $query->where('status', '!=', 'Selesai');
            }

            // [PERBAIKAN UTAMA] Urutkan hanya berdasarkan No Antrian (ASC) 
            // agar urut 1, 2, 3... tanpa terpengaruh kapan data diinput.
            // Jika ingin 'Hadir' tetap paling atas, kita pakai FIELD tapi prioritas antrian tetap utama.
            
            $pendaftarans = $query->orderBy('no_antrian', 'asc')
                                  ->get();
        }

        // Render HTML tabel dari component
        $tableHtml = view('tenaga_medis.components.table_pasien', compact('pendaftarans'))->render();

        return response()->json([
            'table_html' => $tableHtml,
            'count' => $pendaftarans->count()
        ]);
    }

    public function showLoginForm()
    {
        return view('tenaga_medis.login');
    }

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

    public function logout(Request $request)
    {
        Auth::guard('tenaga_medis')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenaga-medis.login');
    }

    public function lihatPasien(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        if (!$tenagaMedisId) {
            return redirect()->route('tenaga-medis.login')->withErrors(['email' => 'Sesi tidak valid.']);
        }

        // Ambil tanggal dari URL. Sekarang default-nya null
        $tanggal = $request->input('tanggal'); 

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        if (empty($layanansDitangani)) {
            $pendaftarans = collect();
        } else {
            // [PERBAIKAN] Hapus ->latest()
            $query = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                            ->with('user');

            // HANYA filter jika $tanggal ADA ISINYA
            if ($tanggal) {
                $query->whereDate('jadwal_dipilih', $tanggal);
            } else {
                // Jika tidak ada filter tanggal, tampilkan semua yang statusnya BUKAN Selesai
                $query->where('status', '!=', 'Selesai');
            }

            // [PERBAIKAN UTAMA] Sorting murni berdasarkan No Antrian (1, 2, 3...)
            $pendaftarans = $query->orderBy('no_antrian', 'asc')
                                  ->get();
        }

        return view('tenaga_medis.pasien.index', compact('pendaftarans', 'tanggal'));
    }

    public function detailPasien(Pendaftaran $pendaftaran)
    {
        return view('tenaga_medis.pasien.show', compact('pendaftaran'));
    }

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

    /**
     * 🔥 METHOD BARU: UNTUK CEK NOTIFIKASI REALTIME (AJAX)
     * Digunakan oleh layout/tenaga_medis.blade.php
     */
    public function checkNotif()
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        
        if (!$tenagaMedisId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ]);
        }

        // 1. Ambil layanan yang ditangani dokter ini (Untuk Antrian)
        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();
        
        $countPasien = 0;

        // 2. Hitung jumlah pasien hari ini yang belum selesai
        if (!empty($layanansDitangani)) {
            $countPasien = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                                    ->where('status', '!=', 'Selesai')
                                    ->whereDate('jadwal_dipilih', Carbon::today())
                                    ->count();
        }

        // 3. 🔥 HITUNG NOTIFIKASI CHAT (Pesan Belum Dibaca)
        // Pesan yang receiver-nya adalah Saya (Tenaga Medis) dan is_read = false
        $unreadChatCount = Message::where('receiver_id', $tenagaMedisId)
                                  ->where('receiver_type', 'medis')
                                  ->where('is_read', false)
                                  ->count();

        return response()->json([
            'success' => true,
            'counts' => [
                'pasien' => $countPasien,
                'chat' => $unreadChatCount // <--- Data baru untuk notifikasi chat
            ]
        ]);
    }
}
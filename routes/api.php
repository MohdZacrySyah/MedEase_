<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Pendaftaran;
use App\Models\Pemeriksaan;
use Carbon\Carbon;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Log;

// Import Controller yang dibutuhkan untuk API (khusus Pasien)
use App\Http\Controllers\Api\BookingController; 

// --- API REGISTER ---
Route::post('/register', function (Request $request) {
    // 1. Validasi Input
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
        'no_hp' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first()
        ], 400);
    }

    // 2. Buat User Baru
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'no_hp' => $request->no_hp,
        'role' => 'pasien',
        'alamat' => $request->alamat ?? null,
    ]);

    $user->makeHidden(['password', 'email_verified_at']);

    return response()->json([
        'status' => 'success',
        'message' => 'Registrasi berhasil! Silakan login.',
        'data' => $user
    ]);
});

// --- API LOGIN MOBILE ---
Route::post('/login-mobile', function (Request $request) {
    $credentials = $request->only('email', 'password');
    
    Log::info('Login attempt received', ['email' => $credentials['email']]);

    if (Auth::attempt($credentials)) {
        
        $user = Auth::user();
        
        Log::info("User ID: {$user->id} successfully authenticated. Checking role: {$user->role}");

        if($user->role !== 'pasien') {
            Log::warning("Login denied: User ID {$user->id} has role '{$user->role}' (Expected 'pasien')");
            Auth::logout(); 
            return response()->json(['status' => 'error', 'message' => 'Bukan akun pasien'], 401);
        }

        $user = User::select('id', 'name', 'email', 'no_hp', 'alamat', 'tanggal_lahir', 'profile_photo_path', 'role')
                    ->where('id', $user->id)
                    ->first();
        
        $user->photo_url = $user->profile_photo_path 
            ? url('storage/' . $user->profile_photo_path) 
            : null;
        
        $user->makeHidden(['password', 'email_verified_at', 'profile_photo_path', 'role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            'data' => $user
        ], 200);
    }

    Log::warning("Login attempt failed for email: {$credentials['email']}");

    return response()->json([
        'status' => 'error',
        'message' => 'Email atau Password salah'
    ], 401);
});

// --- API HISTORY PENDAFTARAN ---
Route::get('/pendaftaran/history', function (Request $request) {
    // ... (Inisialisasi userId tetap sama) ...
    $userId = $request->query('user_id');

    if (!$userId || $userId == 0) {
        return response()->json(['status' => 'success', 'data' => [] ]);
    }

    $data = DB::table('pendaftarans')
        ->join('jadwal_prakteks', 'pendaftarans.jadwal_praktek_id', '=', 'jadwal_prakteks.id')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->where('pendaftarans.user_id', '=', $userId) 
        
        // FIX KRITIS: Hanya ambil status yang aktif, JANGAN ambil 'selesai' atau 'batal'
        ->whereIn('pendaftarans.status', ['menunggu', 'hadir', 'dilayani', 'periksa awal']) 
        
        ->select(
            // ... (Semua kolom select tetap sama) ...
            'pendaftarans.id',
            'pendaftarans.no_antrian',
            'pendaftarans.nama_layanan',
            'pendaftarans.jadwal_dipilih',
            'pendaftarans.status',
            'tenaga_medis.name as dokter_name',
            'pendaftarans.estimasi_dilayani',
            'pendaftarans.status_panggilan',
            'pendaftarans.jumlah_panggilan'
        )
        // Urutkan berdasarkan tanggal pendaftaran terbaru
        ->orderBy('pendaftarans.jadwal_dipilih', 'desc') 
        ->orderBy('pendaftarans.created_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
});
// --- API NOTIFIKASI PEMBATALAN JADWAL DOKTER (FIX KRITIS) ---
Route::get('/notifikasi/pembatalan', function (Request $request) {
    $userId = $request->query('user_id'); 

    if (!$userId || $userId == 0) {
        return response()->json(['status' => 'success', 'data' => [] ]);
    }
    
    try {
        $pembatalan = DB::table('pendaftarans as p')
            ->join('jadwal_prakteks as j', 'p.jadwal_praktek_id', '=', 'j.id')
            ->join('tenaga_medis as t', 'j.tenaga_medis_id', '=', 't.id')
            
            ->where('p.user_id', '=', $userId) 
            // PERBAIKAN KRITIS: Mencakup kemungkinan status 'batal' atau 'Batal' atau 'BATAL'
            ->whereIn('p.status', ['batal', 'Batal', 'BATAL']) 
            
            ->select(
                'p.id',
                't.name as dokter_name',          
                'p.nama_layanan',                 
                'p.jadwal_dipilih',               
                'p.status',                       
                // Kita gunakan 'p.keluhan' karena 'alasan_pembatalan' tidak ada di tabel pendaftarans
                'p.keluhan as alasan_pembatalan'
            )
            ->orderBy('p.updated_at', 'desc') 
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi pembatalan retrieved',
            'data' => $pembatalan
        ]);

    } catch (\Exception $e) {
        // Jika masih crash (500), berarti ada kolom lain yang salah
        return response()->json([
            'status' => 'error', 
            'message' => 'Query crash di server. Error: ' . $e->getMessage()
        ], 500);
    }
});
// --- API PENDAFTARAN MOBILE (PERBAIKAN KRITIS: ESTIMASI WAKTU) ---
Route::post('/pendaftaran/store', function (Request $request) {
    // 1. Validasi
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'jadwal_praktek_id' => 'required|exists:jadwal_prakteks,id',
        'nama_layanan' => 'required',
        'nama_lengkap' => 'required',
        'tanggal_lahir' => 'required|date',
        'alamat' => 'required',
        'no_telepon' => 'required',
        'keluhan' => 'required',
        'lama_keluhan' => 'required',
        'jadwal_dipilih' => 'required|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
    }

    // 2. Simpan Data Awal
    $data = $request->all();
    $data['status'] = 'menunggu'; 
    $data['status_panggilan'] = 'menunggu'; 
    $data['status_periksa'] = 'belum'; 
    $data['jumlah_panggilan'] = 0; 
    
    $pendaftaran = Pendaftaran::create($data); 

    // 3. Hitung Nomor Antrian & ESTIMASI
    $tanggalDipilih = Carbon::parse($request->jadwal_dipilih)->toDateString();
    $jadwalPraktekId = $request->jadwal_praktek_id;
    $namaLayanan = $request->nama_layanan;
    $waktuPendaftaran = Carbon::now()->format('H:i:s'); // Waktu pendaftaran sekarang

    // Hitung Nomor Antrian (Antrian berdasarkan layanan dan tanggal)
    $jumlahSebelumnya = Pendaftaran::where('nama_layanan', $namaLayanan)
        ->whereDate('jadwal_dipilih', $tanggalDipilih)
        ->where('id', '<', $pendaftaran->id)
        ->count();

    $noAntrianBaru = $jumlahSebelumnya + 1;
    $pendaftaran->no_antrian = $noAntrianBaru;
    
    // =======================================================
    // FIX KRITIS LOGIC PERHITUNGAN ESTIMASI WAKTU
    // =======================================================
    
    // a. Ambil Jam Mulai Dokter
    $jadwalDokter = DB::table('jadwal_prakteks')
        ->where('id', $jadwalPraktekId)
        ->select('jam_mulai')
        ->first();
        
    $jamMulaiDokter = $jadwalDokter->jam_mulai ?? '08:00:00'; 
    
    // Gabungkan tanggal pendaftaran dengan jam mulai dan jam sekarang (untuk perbandingan)
    $waktuMulaiJadwalCarbon = Carbon::parse($tanggalDipilih . ' ' . $jamMulaiDokter);
    $waktuSekarangCarbon = Carbon::parse($tanggalDipilih . ' ' . $waktuPendaftaran); // Hanya ambil jam pendaftaran hari ini

    // Tentukan Waktu Basis (Waktu Awal Perhitungan): MAX (Jam Mulai Jadwal, Waktu Pendaftaran Sekarang)
    // Jika Pasien adalah antrian pertama (No 1) yang mendaftar setelah jam mulai, 
    // basisnya adalah waktu pendaftaran saat ini.
    
    if ($noAntrianBaru == 1) {
        // Jika jam pendaftaran > jam mulai praktik, gunakan jam pendaftaran sebagai basis
        if ($waktuSekarangCarbon->greaterThan($waktuMulaiJadwalCarbon)) {
             $waktuBasis = $waktuSekarangCarbon;
        } else {
             $waktuBasis = $waktuMulaiJadwalCarbon; // Gunakan jam mulai jadwal
        }
    } else {
        // Jika antrian > 1, kita asumsikan semua antrian sebelumnya diselesaikan sesuai estimasi
        // dan kita mulai hitungan dari Jam Mulai Jadwal
        $waktuBasis = $waktuMulaiJadwalCarbon;
    }
    
    // b. Hitung Estimasi: Waktu Basis + (Nomor Antrian - 1) * 15 Menit
    $waktuTambahanMenit = ($noAntrianBaru - 1) * 15; 
    
    $estimasiWaktu = $waktuBasis
        ->addMinutes($waktuTambahanMenit)
        ->format('H:i:s'); // Format ke jam:menit:detik
        
    $pendaftaran->estimasi_dilayani = $estimasiWaktu;

    // c. Simpan Perubahan (no_antrian dan estimasi_dilayani)
    $pendaftaran->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Berhasil mendaftar! No Antrian: ' . $pendaftaran->no_antrian . ' (Est: ' . $estimasiWaktu . ' WIB)',
        'estimasi' => $estimasiWaktu
    ]);
});

// ROUTE: Cek Ketersediaan Dokter untuk Pasien Mobile
Route::get('/dokter/check-availability', [BookingController::class, 'checkDoctorAvailability']);

Route::get('/jadwal-hari-ini', function () {
    $englishDay = date('l'); 
    $hariIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $sekarang = $hariIndo[$englishDay]; 

    $jadwal = DB::table('jadwal_prakteks')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->where('jadwal_prakteks.hari', 'LIKE', '%"'.$sekarang.'"%') 
        ->select(
            'jadwal_prakteks.id',
            'tenaga_medis.name as nama_dokter',
            'jadwal_prakteks.layanan', 
            'jadwal_prakteks.jam_mulai', 
            'jadwal_prakteks.jam_selesai'
        )
        ->get();

    return response()->json([
        'hari' => $sekarang,
        'data' => $jadwal
    ]);
});

Route::get('/jadwal-semua', function () {
    $jadwal = DB::table('jadwal_prakteks')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->select(
            'jadwal_prakteks.id',
            'tenaga_medis.name as nama_dokter',
            'jadwal_prakteks.layanan',
            'jadwal_prakteks.hari',
            'jadwal_prakteks.jam_mulai',
            'jadwal_prakteks.jam_selesai'
        )
        ->orderBy('tenaga_medis.name', 'asc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $jadwal
    ]);
});

// --- GET PROFIL ---
Route::get('/profil', function (Request $request) {
    $userId = $request->query('id'); 
    $user = \App\Models\User::find($userId);
    
    if($user) {
        $user = User::select('id', 'name', 'email', 'no_hp', 'alamat', 'tanggal_lahir', 'profile_photo_path')
                    ->find($userId);
                    
        $user->photo_url = $user->profile_photo_path 
            ? url('storage/' . $user->profile_photo_path) 
            : null;
            
        $user->makeHidden(['password', 'email_verified_at', 'profile_photo_path']); 
            
        return response()->json(['status' => 'success', 'data' => $user]);
    }
    return response()->json(['status' => 'error', 'message' => 'User not found']);
});

// --- UPDATE PROFIL ---
Route::post('/profil/update', function (Request $request) {
    $user = \App\Models\User::find($request->id);
    
    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
    }
    
    // 1. Validasi Data Teks yang Masuk
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'no_hp' => 'nullable|string',
        'alamat' => 'nullable|string',
        'tanggal_lahir' => 'required|date',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);
    
    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400); 
    }

    // 2. Update Data Teks
    $user->name = $request->name;
    // $user->email = $request->email;
    $user->no_hp = $request->no_hp;
    $user->alamat = $request->alamat;
    $user->tanggal_lahir = $request->tanggal_lahir; 

    // 3. Update Foto (Jika ada file 'photo' yang diupload)
    if ($request->hasFile('photo')) {
        try {
            if ($user->profile_photo_path) {
                // Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan foto: '.$e->getMessage()], 500); 
        }
    }
    
    // 4. Simpan Perubahan ke Database
    if ($user->save()) {
        $user->photo_url = $user->profile_photo_path ? url('storage/' . $user->profile_photo_path) : null; 
        $user->makeHidden(['password', 'email_verified_at', 'profile_photo_path']); 
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Profil berhasil diupdate',
            'data' => $user
        ], 200);
    } else {
        return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan perubahan ke database.'], 500);
    }
});

// --- API RIWAYAT PEMERIKSAAN ---
Route::get('/pemeriksaan/riwayat', function (Request $request) {
    $userId = $request->query('user_id');

    if (!$userId || $userId == 0) {
        return response()->json(['status' => 'success', 'data' => []]);
    }

    $riwayat = DB::table('pemeriksaans')
        ->join('pendaftarans', 'pemeriksaans.pendaftaran_id', '=', 'pendaftarans.id')
        ->leftJoin('pemeriksaan_awals', 'pendaftarans.id', '=', 'pemeriksaan_awals.pendaftaran_id') 
        ->join('tenaga_medis', 'pemeriksaans.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->where('pendaftarans.user_id', $userId) 
        ->select(
            'pemeriksaans.id',
            'pemeriksaans.created_at as tanggal_periksa',
            'tenaga_medis.name as dokter_name',
            'pendaftarans.nama_layanan',
            'pendaftarans.keluhan',
            'pendaftarans.lama_keluhan',
            'pemeriksaans.subjektif',
            'pemeriksaans.objektif',
            'pemeriksaans.assessment as diagnosa',
            'pemeriksaans.plan as rencana',
            'pemeriksaans.resep_obat',
            'pemeriksaans.harga',
            'pemeriksaan_awals.tekanan_darah',
            'pemeriksaan_awals.berat_badan',
            'pemeriksaan_awals.suhu_tubuh'
        )
        ->orderBy('pemeriksaans.created_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $riwayat
    ]);
});

// --- ENDPOINT BARU UNTUK DETAIL JADWAL ---
Route::get('/jadwal/detail', function (Request $request) {
    $jadwalId = $request->query('id');

    $jadwal = DB::table('jadwal_prakteks')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->where('jadwal_prakteks.id', $jadwalId)
        ->select(
            'jadwal_prakteks.id',
            'tenaga_medis.name as nama_dokter',
            'jadwal_prakteks.layanan',
            'jadwal_prakteks.hari',
            'jadwal_prakteks.jam_mulai',
            'jadwal_prakteks.jam_selesai'
        )
        ->first();

    if ($jadwal) {
        // PERBAIKAN FORMAT HARI
        $jadwal->hari = strtoupper(trim($jadwal->hari, '[]" ')); 
        
        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ], 200);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Jadwal tidak ditemukan',
        'data' => null
    ], 200);
});

// ===============================================
// API DASHBOARD DATA (STATISTIK & ANTRIAN AKTIF)
// ===============================================
Route::get('/dashboard-mobile', function (Request $request) {
    $userId = $request->query('user_id');

    if (!$userId) {
        return response()->json(['status' => 'error', 'message' => 'User ID required'], 400);
    }
    
    // --- 1. STATISTIK KESEHATAN ---
    $pemeriksaanSelesai = Pemeriksaan::whereHas('pendaftaran', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })->count();

    $dokterDikunjungi = Pemeriksaan::whereHas('pendaftaran', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })->distinct('tenaga_medis_id')->count('tenaga_medis_id');

    // --- 2. ANTRIAN AKTIF PASIEN (Antrian pasien yang sedang login) ---
    $antrianAktif = Pendaftaran::where('user_id', $userId)
        ->whereIn('status', ['menunggu', 'hadir', 'dilayani', 'periksa awal']) // FIX: Tambah 'hadir'
        ->whereDate('jadwal_dipilih', '=', Carbon::today()) 
        ->join('jadwal_prakteks', 'pendaftarans.jadwal_praktek_id', '=', 'jadwal_prakteks.id')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->select(
            'pendaftarans.status',
            'tenaga_medis.name as dokter_name',
            'pendaftarans.no_antrian',
            'pendaftarans.estimasi_dilayani',
            'pendaftarans.updated_at as terakhir_diperbarui'
        )
        ->orderBy('pendaftarans.jadwal_dipilih', 'asc') 
        ->first(); 
    
    // Konversi Antrian Aktif Pasien ke array
    $antrianData = $antrianAktif ? [
        'status' => $antrianAktif->status,
        'dokter_name' => $antrianAktif->dokter_name,
        'no_antrian' => $antrianAktif->no_antrian,
        'estimasi_dilayani' => $antrianAktif->estimasi_dilayani,
        'terakhir_diperbarui' => $antrianAktif->terakhir_diperbarui ? Carbon::parse($antrianAktif->terakhir_diperbarui)->format('H:i:s') : null
    ] : null; 

    // =======================================================
    // FIX KRITIS: MENGAMBIL ANTRIAN YANG SEDANG DILAYANI/DIPROSES KLINIK (GLOBAL)
    // Diambil dari antrian yang statusnya sudah 'hadir', 'periksa awal', atau 'dilayani'
    // Tetapi belum 'Selesai'. Diambil yang paling baru diupdate/diproses.
    // =======================================================
    $antrianDilayaniGlobal = DB::table('pendaftarans')
        // Cari antrian yang sedang diproses klinik (hadir, periksa awal, dilayani)
        ->whereIn('pendaftarans.status', ['hadir', 'periksa awal', 'dilayani']) 
        ->whereDate('pendaftarans.jadwal_dipilih', '=', Carbon::today())
        ->join('jadwal_prakteks', 'pendaftarans.jadwal_praktek_id', '=', 'jadwal_prakteks.id')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->select(
            'pendaftarans.no_antrian',
            'tenaga_medis.name as dokter_name',
            'pendaftarans.nama_layanan'
        )
        ->orderBy('pendaftarans.updated_at', 'desc') 
        ->first();

    $antrianDilayaniGlobalData = $antrianDilayaniGlobal ? [
        'no_antrian' => $antrianDilayaniGlobal->no_antrian,
        'dokter_name' => $antrianDilayaniGlobal->dokter_name,
        'nama_layanan' => $antrianDilayaniGlobal->nama_layanan
    ] : null;

    // --- 3. INFORMASI KLINIK (Hardcoded) ---
    $infoKlinik = [
        'jam_operasional' => '16.00 - 20.00 WIB',
        'kontak_kami' => '0822 1117 8167',
        'alamat_klinik' => 'Jl. I Mohammad Ali, Bengkalis, Riau'
    ];

    return response()->json([
        'status' => 'success',
        'message' => 'Dashboard data retrieved successfully',
        'data' => [
            'pemeriksaan_selesai' => $pemeriksaanSelesai,
            'dokter_dikunjungi' => $dokterDikunjungi,
            
            // Antrian Pasien yang Sedang Login
            'antrian_aktif' => $antrianData,
            
            // Antrian Klinik yang Sedang Dilayani (Global)
            'antrian_global' => $antrianDilayaniGlobalData,
            
            'jam_operasional' => $infoKlinik['jam_operasional'],
            'kontak_kami' => $infoKlinik['kontak_kami'],
            'alamat_klinik' => $infoKlinik['alamat_klinik'],
        ]
    ]);
});

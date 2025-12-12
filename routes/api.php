<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Pendaftaran;
use Carbon\Carbon;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // WAJIB DIIMPOR UNTUK FOTO!

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

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        if($user->role == 'pasien') {
            $user->makeHidden(['password', 'email_verified_at']); 

            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil',
                'data' => $user
            ], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Bukan akun pasien'], 401);
        }
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Email atau Password salah'
    ], 401);
});

// --- API HISTORY PENDAFTARAN (Jadwal/Notifikasi) ---
Route::get('/pendaftaran/history', function (Request $request) {
    $userId = $request->query('user_id');

    if (!$userId || $userId == 0) {
        return response()->json(['status' => 'success', 'data' => [] ]);
    }

    $data = DB::table('pendaftarans')
        ->join('jadwal_prakteks', 'pendaftarans.jadwal_praktek_id', '=', 'jadwal_prakteks.id')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        
        // FILTER KRITIS 1: Wajib menggunakan user_id dari pendaftaran
        ->where('pendaftarans.user_id', '=', $userId) 
        
        ->select(
            'pendaftarans.id',
            'pendaftarans.no_antrian',
            'pendaftarans.nama_layanan',
            'pendaftarans.jadwal_dipilih',
            'pendaftarans.status',
            'tenaga_medis.name as dokter_name'
        )
        ->orderBy('pendaftarans.created_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
});

// --- API PENDAFTARAN MOBILE ---
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

    // 2. Simpan Data
    $data = $request->all();
    $pendaftaran = Pendaftaran::create($data);

    // 3. Hitung Nomor Antrian
    $tanggalDipilih = Carbon::parse($request->jadwal_dipilih)->toDateString();
    $namaLayanan = $request->nama_layanan;

    $jumlahSebelumnya = Pendaftaran::where('nama_layanan', $namaLayanan)
        ->whereDate('jadwal_dipilih', $tanggalDipilih)
        ->where('id', '<', $pendaftaran->id)
        ->count();

    $pendaftaran->no_antrian = $jumlahSebelumnya + 1;
    $pendaftaran->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Berhasil mendaftar! No Antrian: ' . $pendaftaran->no_antrian
    ]);
});

// --- API LAINNYA ---

// ROUTE: Cek Ketersediaan Dokter untuk Pasien Mobile (Jika Anda menggunakan BookingController)
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
        $user->photo_url = $user->profile_photo_path 
            ? url('storage/' . $user->profile_photo_path) 
            : null;
            
        $user->makeHidden(['password', 'email_verified_at']); 
            
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
    ]);
    
    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400); 
    }

    // 2. Update Data Teks
    $user->name = $request->name;
    $user->email = $request->email;
    $user->no_hp = $request->no_hp;
    $user->alamat = $request->alamat;

    // 3. Update Foto (Jika ada file 'photo' yang diupload)
    if ($request->hasFile('photo')) {
        try {
            // Hapus foto lama jika ada
            if ($user->profile_photo_path) {
                // Storage::disk('public')->delete($user->profile_photo_path); 
            }
            
            // Simpan foto baru
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            
        } catch (\Exception $e) {
            // Jika penyimpanan foto gagal (misal: izin folder salah)
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan foto: '.$e->getMessage()], 500); 
        }
    }
    
    // 4. Simpan Perubahan ke Database
    if ($user->save()) {
        $user->makeHidden(['password', 'email_verified_at']); 
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Profil berhasil diupdate',
            'data' => $user
        ], 200);
    } else {
        return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan perubahan ke database.'], 500);
    }
});

// --- API RIWAYAT PEMERIKSAAN (PERBAIKAN TUNTAS UNTUK FILTER) ---
Route::get('/pemeriksaan/riwayat', function (Request $request) {
    $userId = $request->query('user_id');

    if (!$userId || $userId == 0) {
        return response()->json(['status' => 'success', 'data' => []]);
    }

    $riwayat = DB::table('pemeriksaans')
        ->join('pendaftarans', 'pemeriksaans.pendaftaran_id', '=', 'pendaftarans.id')
        ->leftJoin('pemeriksaan_awals', 'pendaftarans.id', '=', 'pemeriksaan_awals.pendaftaran_id') 
        ->join('tenaga_medis', 'pemeriksaans.tenaga_medis_id', '=', 'tenaga_medis.id')
        
        // FILTER KRITIS 2: Ambil pemeriksaan HANYA yang terkait user_id pendaftarannya
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

// --- ENDPOINT BARU UNTUK DETAIL JADWAL (Digunakan FormDaftarActivity) ---
// Ini akan mengembalikan satu objek JadwalModel
Route::get('/jadwal/detail', function (Request $request) {
    $jadwalId = $request->query('id');

    $jadwal = DB::table('jadwal_prakteks')
        ->join('tenaga_medis', 'jadwal_prakteks.tenaga_medis_id', '=', 'tenaga_medis.id')
        ->where('jadwal_prakteks.id', $jadwalId)
        ->select(
            'jadwal_prakteks.id',
            'tenaga_medis.name as nama_dokter',
            'jadwal_prakteks.layanan',
            'jadwal_prakteks.hari', // INI KRITIS: Berisi Hari Praktik ([Senin, Selasa])
            'jadwal_prakteks.jam_mulai',
            'jadwal_prakteks.jam_selesai'
        )
        ->first(); // Mengambil hanya satu baris

    if ($jadwal) {
        // PERBAIKAN KRITIS: Memastikan data 'hari' selalu UPPERCASE agar cocok dengan Kotlin
        $jadwal->hari = strtoupper(trim($jadwal->hari, '[]" ')); 
        
        // Jika data ditemukan, kembalikan dalam format JadwalModel tunggal
        return response()->json($jadwal, 200);
    }

    // Jika ID jadwal tidak ditemukan
    return response()->json(['message' => 'Jadwal tidak ditemukan'], 404);
});
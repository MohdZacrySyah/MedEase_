<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

// --- Import Controller ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TenagaMedisController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Admin\PemeriksaanAwalController;
use App\Http\Controllers\Admin\JadwalPraktekController;
use App\Http\Controllers\Admin\TenagaMedisController as AdminTenagaMedisController;
use App\Http\Controllers\TenagaMedis\RiwayatPemeriksaanController;
use App\Http\Controllers\Apoteker\Auth\LoginController as ApotekerLoginController;
use App\Http\Controllers\Apoteker\AntrianController;
use App\Http\Controllers\Apoteker\RiwayatController;
use App\Http\Controllers\Apoteker\LaporanController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ChatController;

// 👇 IMPORT CONTROLLER PEMBATALAN JADWAL
use App\Http\Controllers\Admin\JadwalController as AdminJadwalController;


// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Rute Publik & Pasien
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('home');

// Auth Pasien
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'proses'])->name('login.proses');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'registerProses'])->name('register.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lupa Password Pasien
Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendOtp'])->name('password.send_otp');
Route::get('verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('password.verify.form');
Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.otp');
Route::get('reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

// Halaman Publik
Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
Route::view('/notifikasi', 'notifikasi')->name('notifikasi');

// Pendaftaran
Route::controller(PendaftaranController::class)->group(function () {
    Route::get('/daftar', 'index')->name('daftar.index');
    Route::get('/daftar-data/{jadwal}', 'getFormDataJson')->name('daftar.form.json');
    Route::get('/daftar/{jadwal}', 'showForm')->name('daftar.form');
    Route::post('/daftar', 'store')->name('daftar.store');
    Route::get('/jadwal-praktek/{jadwalPraktek}/closed-dates', 'getClosedDates')->name('jadwal.closed-dates');
});

// Panel Pasien
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profil');
    Route::get('/profil/edit', [AuthController::class, 'editProfile'])->name('profil.edit');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profil.update');
    Route::post('/profil/photo', [AuthController::class, 'updatePhoto'])->name('profil.photo.update');
    Route::get('/riwayat-pemeriksaan', [AuthController::class, 'riwayatPemeriksaan'])->name('riwayat.index');
    Route::get('/notifikasi-jadwal', [AuthController::class, 'notifikasiList'])->name('notifikasi.list');
    Route::get('/notifikasi-jadwal/{pendaftaran}', [AuthController::class, 'notifikasiDetail'])->name('notifikasi.detail');
    Route::post('/notifikasi-jadwal/mark-as-read/{id}', [AuthController::class, 'markNotificationAsRead'])->name('notifikasi.read');
    
    // 🔥 ROUTE POLLING PANGGILAN ANTRIAN (POP-UP PASIEN)
    Route::get('/check-panggilan', function () {
        $user = Auth::user();
        $panggilan = \App\Models\Pendaftaran::where('user_id', $user->id)
            ->whereDate('jadwal_dipilih', Carbon::today())
            ->where('status_panggilan', 'dipanggil')
            ->first();
            
        return response()->json([
            'dipanggil' => $panggilan ? true : false,
            'data' => $panggilan
        ]);
    })->name('check.panggilan');

    // 🔥 ROUTE KONFIRMASI DATANG (PASIEN KLIK 'MENUJU KESANA')
    Route::post('/konfirmasi-datang/{id}', [AuthController::class, 'konfirmasiDatang'])->name('konfirmasi.datang');
});

// Chat
Route::middleware(['auth:web,tenaga_medis'])->group(function () {
    Route::get('/chat/{partnerId?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
});

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout'); 

// Rute Lupa Password Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('forgot-password', [AdminController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [AdminController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AdminController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [AdminController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Route Utama Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/keloladatapasien', [AdminController::class, 'keloladatapasien'])->name('keloladatapasien');
    Route::get('/catatanpemeriksaan', [AdminController::class, 'catatanpemeriksaan'])->name('catatanpemeriksaan');
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');

    // Route Resource
    Route::resource('tenaga-medis', AdminTenagaMedisController::class); 
    Route::resource('kelolajadwalpraktek', JadwalPraktekController::class);
    
    // Route Pemeriksaan & Pasien
    Route::get('/pasien/{user}/riwayat', [AdminController::class, 'riwayatPasien'])->name('pasien.riwayat');
    Route::delete('/pasien/{user}', [AdminController::class, 'hapusPasien'])->name('pasien.hapus');
    Route::get('/pendaftaran/{pendaftaran}/periksa-awal', [PemeriksaanAwalController::class, 'create'])->name('pemeriksaan-awal.create');
    Route::post('/pendaftaran/{pendaftaran}/periksa-awal', [PemeriksaanAwalController::class, 'store'])->name('pemeriksaan-awal.store');
    Route::get('/pendaftaran/{pendaftaran}/periksa-awal-data', [PemeriksaanAwalController::class, 'getModalDataJson'])->name('pemeriksaan-awal.json');

    // 🔥 ROUTE SISTEM PEMANGGILAN & PEMBATALAN (Fixed)
    Route::post('/jadwal/cancel', [AdminJadwalController::class, 'cancelJadwal'])->name('jadwal.cancel'); 
    Route::post('/panggil-pasien/{id}', [AdminController::class, 'panggilPasien'])->name('panggil.pasien');
    Route::post('/stop-panggil/{id}', [AdminController::class, 'stopPanggil'])->name('stop.panggil'); // ✅ Route Baru
    Route::post('/alihkan-pasien/{id}', [AdminController::class, 'alihkanPasien'])->name('alihkan.pasien');
});

/*
|--------------------------------------------------------------------------
| Rute Tenaga Medis & Apoteker (Tetap Sama)
|--------------------------------------------------------------------------
*/

// --- Rute Tenaga Medis ---
Route::get('tenaga-medis/login', [TenagaMedisController::class, 'showLoginForm'])->name('tenaga-medis.login');
Route::post('tenaga-medis/login', [TenagaMedisController::class, 'login'])->name('tenaga-medis.login.post');
Route::get('tenaga-medis/forgot-password', [TenagaMedisController::class, 'showLinkRequestForm'])->name('tenaga-medis.password.request');
Route::post('tenaga-medis/forgot-password', [TenagaMedisController::class, 'sendResetLinkEmail'])->name('tenaga-medis.password.email');
Route::get('tenaga-medis/reset-password/{token}', [TenagaMedisController::class, 'showResetForm'])->name('tenaga-medis.password.reset');
Route::post('tenaga-medis/reset-password', [TenagaMedisController::class, 'reset'])->name('tenaga-medis.password.update');
Route::post('tenaga-medis/logout', [TenagaMedisController::class, 'logout'])->name('tenaga-medis.logout');

Route::middleware(['auth:tenaga_medis'])->prefix('tenaga-medis')->name('tenaga-medis.')->group(function () {
    Route::post('/jadwal/cancel', [AdminJadwalController::class, 'cancelJadwal'])->name('jadwal.cancel'); 
    Route::get('/pasien/{pendaftaran}', [TenagaMedisController::class, 'detailPasien'])->name('pasien.show'); 
    Route::get('/dashboard', [TenagaMedisController::class, 'dashboard'])->name('dashboard');
    Route::get('/pasien', [TenagaMedisController::class, 'lihatPasien'])->name('pasien.index');
    Route::get('/pasien/{pendaftaran}/periksa', [PemeriksaanController::class, 'create'])->name('pemeriksaan.create');
    Route::post('/pasien/{pendaftaran}/periksa', [PemeriksaanController::class, 'store'])->name('pemeriksaan.store');
    Route::get('/pasien/{user}/riwayat', [TenagaMedisController::class, 'riwayatPasien'])->name('pasien.riwayat');
    Route::get('/riwayat-pemeriksaan-saya', [TenagaMedisController::class, 'myPemeriksaanHistory'])->name('riwayat-pemeriksaan-saya');
    Route::get('/laporan', [TenagaMedisController::class, 'laporan'])->name('laporan');
    Route::get('/pemeriksaan/json/{pendaftaran}', [PemeriksaanController::class, 'getModalDataJson'])->name('pemeriksaan.json');
    Route::get('/riwayat-pemeriksaan', [RiwayatPemeriksaanController::class, 'index'])->name('riwayat.index');
});

// --- Rute Apoteker ---
Route::prefix('apoteker')->name('apoteker.')->group(function () {
    Route::get('login', [ApotekerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [ApotekerLoginController::class, 'login']);
    Route::post('logout', [ApotekerLoginController::class, 'logout'])->name('logout')->middleware('auth:apoteker'); 
    Route::get('forgot-password', [ApotekerLoginController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ApotekerLoginController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ApotekerLoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ApotekerLoginController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth:apoteker'])->prefix('apoteker')->name('apoteker.')->group(function () {
    Route::get('dashboard', function () { return Redirect::route('apoteker.antrian.index'); })->name('dashboard');
    Route::get('antrian', [AntrianController::class, 'index'])->name('antrian.index');
    Route::post('antrian/{resep}/selesaikan', [AntrianController::class, 'selesaikan'])->name('antrian.selesaikan');
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
});
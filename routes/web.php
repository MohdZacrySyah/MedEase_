<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

// --- Import Controller ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ChatController;

// --- Import Controller Admin ---
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PemeriksaanAwalController;
use App\Http\Controllers\Admin\JadwalPraktekController;
use App\Http\Controllers\Admin\TenagaMedisController as AdminTenagaMedisController;
use App\Http\Controllers\Admin\JadwalController as AdminJadwalController;

// --- Import Controller Tenaga Medis ---
use App\Http\Controllers\TenagaMedisController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\TenagaMedis\RiwayatPemeriksaanController;

// --- Import Controller Apoteker ---
use App\Http\Controllers\Apoteker\Auth\LoginController as ApotekerLoginController;
use App\Http\Controllers\Apoteker\AntrianController;
use App\Http\Controllers\Apoteker\RiwayatController;
use App\Http\Controllers\Apoteker\LaporanController;
use App\Http\Controllers\Auth\GoogleController;

// --- Import Model (Untuk Logic Closure) ---
use App\Models\Pendaftaran;

/*
|--------------------------------------------------------------------------
| Google OAuth Routes
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Rute Publik & Auth Pasien
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('home');

// Auth Pasien
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'proses')->name('login.proses');
    Route::get('/register', 'registerForm')->name('register.form');
    Route::post('/register', 'registerProses')->name('register.proses');
    Route::post('/logout', 'logout')->name('logout');

    // Lupa Password
    Route::get('forgot-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('forgot-password', 'sendOtp')->name('password.send_otp');
    Route::get('verify-otp', 'showVerifyOtpForm')->name('password.verify.form');
    Route::post('verify-otp', 'verifyOtp')->name('password.verify.otp');
    Route::get('reset-password', 'showResetPasswordForm')->name('password.reset.form');
    Route::post('reset-password', 'updatePassword')->name('password.update');
});

// Halaman Publik Lainnya
Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
Route::view('/notifikasi', 'notifikasi')->name('notifikasi');

// Pendaftaran (Public Access for Form)
Route::controller(PendaftaranController::class)->group(function () {
    Route::get('/daftar', 'index')->name('daftar.index');
    Route::get('/daftar-data/{jadwal}', 'getFormDataJson')->name('daftar.form.json');
    Route::get('/daftar/{jadwal}', 'showForm')->name('daftar.form');
    Route::post('/daftar', 'store')->name('daftar.store');
    Route::get('/jadwal-praktek/{jadwalPraktek}/closed-dates', 'getClosedDates')->name('jadwal.closed-dates');
});

/*
|--------------------------------------------------------------------------
| Panel Pasien (Middleware Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // 🔥 API CHECK NOTIFIKASI PASIEN (REALTIME)
    Route::get('/api/patient/check-notif', [AuthController::class, 'checkNotif'])->name('api.patient.check_notif');
    // Dashboard & Profil
    Route::controller(AuthController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/profil', 'showProfile')->name('profil');
        Route::get('/profil/edit', 'editProfile')->name('profil.edit');
        Route::put('/profil', 'updateProfile')->name('profil.update');
        Route::post('/profil/photo', 'updatePhoto')->name('profil.photo.update');
        Route::get('/riwayat-pemeriksaan', 'riwayatPemeriksaan')->name('riwayat.index');
        
        // Notifikasi
        Route::get('/notifikasi-jadwal', 'notifikasiList')->name('notifikasi.list');
        Route::get('/notifikasi-jadwal/{pendaftaran}', 'notifikasiDetail')->name('notifikasi.detail');
        Route::post('/notifikasi-jadwal/mark-as-read/{id}', 'markNotificationAsRead')->name('notifikasi.read');
        
        // 🔥 Route API Status Antrian
        Route::get('/api/queue-status-today/{jadwal}', 'getQueueStatus')->name('api.queue.status');
    });

    // 1. Cek Panggilan (Polling)
    Route::get('/check-panggilan', function () {
        $user = Auth::user();
        $panggilan = Pendaftaran::where('user_id', $user->id)
            ->whereDate('jadwal_dipilih', Carbon::today())
            ->where('status_panggilan', 'dipanggil')
            ->first();
            
        return response()->json([
            'dipanggil' => $panggilan ? true : false,
            'data' => $panggilan
        ]);
    })->name('check.panggilan');

    // 2. Stop Alarm
    Route::post('/stop-alarm/{id}', function($id) {
        $pendaftaran = Pendaftaran::findOrFail($id);
        if($pendaftaran->user_id == Auth::id()) {
            $pendaftaran->status_panggilan = 'menunggu'; 
            $pendaftaran->save();
        }
        return response()->json(['success' => true]);
    })->name('stop.alarm');
});

// Chat System
Route::middleware(['auth:web,tenaga_medis'])->group(function () {
    Route::get('/chat/{partnerId?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/mark-read/{partnerId}', [ChatController::class, 'markRead'])->name('chat.markRead');

    // 🔥 TAMBAHKAN BARIS INI (WAJIB UNTUK AUTO LOAD):
    Route::get('/chat/get-messages/{partnerId}', [ChatController::class, 'getMessages'])->name('chat.getMessages');
});

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout'); 

// Password Reset Admin
Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('forgot-password', 'sendResetLinkEmail')->name('password.email');
    Route::get('reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('reset-password', 'reset')->name('password.update');
});

// Panel Admin
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/keloladatapasien', 'keloladatapasien')->name('keloladatapasien');
        Route::get('/catatanpemeriksaan', 'catatanpemeriksaan')->name('catatanpemeriksaan');
        Route::get('/laporan', 'laporan')->name('laporan');
        Route::get('/pasien/{user}/riwayat', 'riwayatPasien')->name('pasien.riwayat');
        Route::delete('/pasien/{user}', 'hapusPasien')->name('pasien.hapus');
        
        // --- Sistem Pemanggilan ---
        Route::post('/panggil-pasien/{id}', 'panggilPasien')->name('panggil.pasien');
        Route::post('/tandai-hadir/{id}', 'tandaiHadir')->name('tandai.hadir');
        Route::post('/stop-panggil/{id}', 'stopPanggil')->name('stop.panggil');
        Route::post('/alihkan-pasien/{id}', 'alihkanPasien')->name('alihkan.pasien');
        
    });
Route::get('/api/check-notif', [AdminController::class, 'checkNotif'])->name('api.check_notif');
    Route::resource('tenaga-medis', AdminTenagaMedisController::class); 
    Route::resource('kelolajadwalpraktek', JadwalPraktekController::class);

    Route::controller(PemeriksaanAwalController::class)->group(function() {
        Route::get('/pendaftaran/{pendaftaran}/periksa-awal', 'create')->name('pemeriksaan-awal.create');
        Route::post('/pendaftaran/{pendaftaran}/periksa-awal', 'store')->name('pemeriksaan-awal.store');
        Route::get('/pendaftaran/{pendaftaran}/periksa-awal-data', 'getModalDataJson')->name('pemeriksaan-awal.json');
    });

    Route::post('/jadwal/cancel', [AdminJadwalController::class, 'cancelJadwal'])->name('jadwal.cancel'); 
});

/*
|--------------------------------------------------------------------------
| Rute Tenaga Medis
|--------------------------------------------------------------------------
*/
Route::controller(TenagaMedisController::class)->prefix('tenaga-medis')->name('tenaga-medis.')->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login')->name('login.post');
    Route::get('forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('forgot-password', 'sendResetLinkEmail')->name('password.email');
    Route::get('reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('reset-password', 'reset')->name('password.update');
    Route::post('logout', 'logout')->name('logout');
    Route::post('/pemeriksaan/{pendaftaran}/mulai', [PemeriksaanController::class, 'mulaiPemeriksaan'])
    ->name('tenaga-medis.pemeriksaan.mulai');
});

Route::middleware(['auth:tenaga_medis'])->prefix('tenaga-medis')->name('tenaga-medis.')->group(function () {
    Route::post('/jadwal/cancel', [AdminJadwalController::class, 'cancelJadwal'])->name('jadwal.cancel'); 
    
    Route::controller(TenagaMedisController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/pasien', 'lihatPasien')->name('pasien.index');
        Route::get('/pasien/{pendaftaran}', 'detailPasien')->name('pasien.show'); 
        Route::get('/pasien/{user}/riwayat', 'riwayatPasien')->name('pasien.riwayat');
        Route::get('/riwayat-pemeriksaan-saya', 'myPemeriksaanHistory')->name('riwayat-pemeriksaan-saya');
        Route::get('/laporan', 'laporan')->name('laporan');
    });

    Route::controller(PemeriksaanController::class)->group(function () {
        Route::get('/pasien/{pendaftaran}/periksa', 'create')->name('pemeriksaan.create');
        Route::post('/pasien/{pendaftaran}/periksa', 'store')->name('pemeriksaan.store');
        Route::get('/pemeriksaan/json/{pendaftaran}', 'getModalDataJson')->name('pemeriksaan.json');
    });

    Route::get('/riwayat-pemeriksaan', [RiwayatPemeriksaanController::class, 'index'])->name('riwayat.index');
});

/*
|--------------------------------------------------------------------------
| Rute Apoteker
|--------------------------------------------------------------------------
*/
Route::prefix('apoteker')->name('apoteker.')->controller(ApotekerLoginController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->name('logout')->middleware('auth:apoteker'); 
    Route::get('forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('forgot-password', 'sendResetLinkEmail')->name('password.email');
    Route::get('reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('reset-password', 'reset')->name('password.update');
});

Route::middleware(['auth:apoteker'])->prefix('apoteker')->name('apoteker.')->group(function () {
    Route::get('dashboard', function () { return Redirect::route('apoteker.antrian.index'); })->name('dashboard');
    Route::get('antrian', [AntrianController::class, 'index'])->name('antrian.index');
    Route::post('antrian/{resep}/selesaikan', [AntrianController::class, 'selesaikan'])->name('antrian.selesaikan');
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
});
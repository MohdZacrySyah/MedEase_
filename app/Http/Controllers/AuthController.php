<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\Pemeriksaan;
use App\Models\JadwalPraktek;
use App\Models\DoctorAvailability; // 🔥 Import Model Ini
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Import yang kita perlukan untuk OTP
use App\Models\PasswordResetOtp;
use App\Notifications\PasienOtpNotification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function proses(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function registerForm()
    {
        return view('register');
    }

    public function registerProses(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Model User akan hash otomatis
        ]);

        return redirect()->route('login')
                         ->with('success', 'Registrasi berhasil! Silakan login.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }


    /*
    |--------------------------------------------------------------------------
    | FUNGSI BARU UNTUK LUPA PASSWORD (OTP)
    |--------------------------------------------------------------------------
    */

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->onlyInput('email');
        }

        $otp = random_int(100000, 999999); 

        PasswordResetOtp::updateOrCreate(
            ['email' => $user->email],
            [
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        try {
            $user->notify(new PasienOtpNotification($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email OTP. Coba lagi nanti.']);
        }

        return redirect()->route('password.verify.form')
                         ->with('email', $user->email)
                         ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showVerifyOtpForm(Request $request)
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $otpData = PasswordResetOtp::where('email', $request->email)
                                     ->where('otp', $request->otp)
                                     ->first();
        
        if (!$otpData) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.'])->withInput();
        }

        $expiredAt = Carbon::parse($otpData->created_at)->addMinutes(1);
        
        if (Carbon::now()->isAfter($expiredAt)) {
            $otpData->delete(); 
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta lagi.'])->withInput();
        }

        $request->session()->put('otp_verified_email', $request->email);
        $request->session()->save(); 

        return redirect()->route('password.reset.form');
    }

    public function showResetPasswordForm(Request $request) 
    {
        $email = $request->session()->get('otp_verified_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Harap verifikasi OTP Anda terlebih dahulu.']);
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    public function updatePassword(Request $request)
    {
        $email = $request->session()->get('otp_verified_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah berakhir. Harap ulangi proses.']);
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = $request->password; 
            $user->save();

            PasswordResetOtp::where('email', $email)->delete();
            $request->session()->forget('otp_verified_email');
            $request->session()->save(); 

            return redirect()->route('login')->with('success', 'Password Anda telah berhasil diperbarui. Silakan login.');
        }

        return redirect()->route('password.request')->withErrors(['email' => 'Terjadi kesalahan.']);
    }


    /*
    |--------------------------------------------------------------------------
    | FUNGSI PANEL PASIEN (DIPERBARUI)
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $user = Auth::user();
        $pasienId = $user->id;

        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');
        $today = Carbon::today(); // Ambil tanggal hari ini

        // 🔥 LOGIKA BARU: Filter Dokter yang Jadwalnya Dibatalkan/Ditutup Hari Ini 🔥
        $unavailableDoctorIds = DoctorAvailability::whereDate('date', $today)
            ->where('is_available', false)
            ->pluck('tenaga_medis_id')
            ->toArray();

        // Ambil Jadwal Hari Ini, TAPI kecualikan dokter yang tutup
        $jadwalHariIni = JadwalPraktek::with('tenagaMedis')
                                     ->whereJsonContains('hari', $namaHariIni)
                                     ->whereNotIn('tenaga_medis_id', $unavailableDoctorIds) 
                                     ->get();
        
        // Notifikasi Hari Ini untuk dashboard
        // MODIFIKASI: Tambahkan eager loading untuk mendapatkan nama dokter/layanan di view
        $notifikasiHariIni = Pendaftaran::with('jadwalPraktek.tenagaMedis') 
                                         ->where('user_id', $user->id)
                                         ->whereDate('jadwal_dipilih', $today)
                                         // Status Diperiksa Awal/Menunggu/Sedang Diperiksa 
                                         ->whereNotIn('status', ['Selesai', 'Dibatalkan']) 
                                         ->first();

        $pemeriksaanTahunIni = Pemeriksaan::where('pasien_id', $pasienId)
                                             ->whereYear('created_at', Carbon::now()->year)
                                             ->count();
        $jumlahDokterDikunjungi = Pemeriksaan::where('pasien_id', $pasienId)
                                                 ->distinct('tenaga_medis_id')
                                                 ->count('tenaga_medis_id');

        return view('dashboard', compact(
            'user',
            'jadwalHariIni',
            'notifikasiHariIni',
            'pemeriksaanTahunIni',
            'jumlahDokterDikunjungi'
        ));
    }

    /**
     * NEW: Endpoint API untuk mengembalikan status antrian dinamis (dipanggil oleh AJAX/Echo).
     *
     * @param JadwalPraktek $jadwal Jadwal praktek yang sedang diakses pasien.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQueueStatus(JadwalPraktek $jadwal)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $pasien = Auth::user();
        $today = Carbon::today();
        
        $currentQueueInfo = "Tidak ada antrian aktif saat ini.";
        $currentQueueNumber = null;

        // 1. Cek Prioritas 1: Antrian yang Sedang diperiksa oleh dokter ('Sedang Diperiksa')
        $antrianAktif = Pendaftaran::with('user')
                                   ->where('jadwal_praktek_id', $jadwal->id)
                                   ->where('status', 'Sedang Diperiksa') // FIX: Menggunakan field 'status'
                                   ->whereDate('jadwal_dipilih', $today)
                                   ->orderBy('no_antrian')
                                   ->first();
        
        if ($antrianAktif) {
            $currentQueueNumber = $antrianAktif->no_antrian;
            $currentQueueInfo = "No. {$currentQueueNumber} (a/n {$antrianAktif->user->name}) **SEDANG DILAYANI**";
        } else {
            
            // 2. Cek Prioritas 2 (Sesuai permintaan Anda): Antrian yang Sudah diperiksa awal dan menunggu giliran dokter ('Diperiksa Awal')
            $antrianAktif = Pendaftaran::with('user')
                                       ->where('jadwal_praktek_id', $jadwal->id)
                                       ->where('status', 'Diperiksa Awal') // FIX: Menggunakan field 'status'
                                       ->whereDate('jadwal_dipilih', $today)
                                       ->orderBy('no_antrian')
                                       ->first();
            
            if ($antrianAktif) {
                $currentQueueNumber = $antrianAktif->no_antrian;
                $currentQueueInfo = "No. {$currentQueueNumber} (a/n {$antrianAktif->user->name}) **DI RUANG PERSIAPAN**";
            } else {
                
                // 3. Cek Prioritas 3: Antrian yang baru daftar dan menunggu diperiksa awal ('Menunggu' atau 'Hadir')
                $antrianAktif = Pendaftaran::with('user')
                                           ->where('jadwal_praktek_id', $jadwal->id)
                                           ->whereIn('status', ['Menunggu', 'Hadir']) // FIX: Menggunakan field 'status' & mencakup 'Hadir'
                                           ->whereDate('jadwal_dipilih', $today)
                                           ->orderBy('no_antrian')
                                           ->first();

                if ($antrianAktif) {
                    $currentQueueNumber = $antrianAktif->no_antrian;
                    $statusText = $antrianAktif->status == 'Hadir' ? 'HADIR' : 'BARU';
                    $currentQueueInfo = "No. {$currentQueueNumber} (a/n {$antrianAktif->user->name}) **MENUNGGU DIPERIKSA AWAL ({$statusText})**";
                }
            }
        }

        // 4. Ambil estimasi waktu pasien yang sedang login (My Estimated Time)
        $myPendaftaran = Pendaftaran::where('user_id', $pasien->id)
                                    ->where('jadwal_praktek_id', $jadwal->id)
                                    ->whereDate('jadwal_dipilih', $today)
                                    ->whereIn('status', ['Menunggu', 'Hadir', 'Diperiksa Awal', 'Sedang Diperiksa']) // FIX: Menggunakan field 'status'
                                    ->first();
        
        $myEstimatedTime = $myPendaftaran && $myPendaftaran->estimasi_dilayani
                           ? Carbon::parse($myPendaftaran->estimasi_dilayani)->format('H:i')
                           : 'Estimasi belum tersedia';

        $myQueueNumber = $myPendaftaran ? $myPendaftaran->no_antrian : null;

        return response()->json([
            'current_queue_number' => $currentQueueNumber,
            'current_queue_info' => $currentQueueInfo,
            'my_estimated_time' => $myEstimatedTime,
            'my_queue_number' => $myQueueNumber,
        ]);
    }
    public function riwayatPemeriksaan(Request $request)
    {
        $query = Pemeriksaan::with([
                            'tenagaMedis', 
                            'pendaftaran.pemeriksaanAwal', 
                            'resep'
                        ])
                        ->where('pasien_id', auth()->id());

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }
        
        $riwayats = $query->latest()->get();

        return view('riwayat_pemeriksaan', [ 
            'riwayats' => $riwayats,
            'tanggal' => $request->tanggal 
        ]);
    }

    /**
     * Menampilkan daftar notifikasi.
     */
    public function notifikasiList()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // 1. Notifikasi Database (Pembatalan/Pembaruan)
        $notificationsDb = $user->notifications()->latest()->get();
        
        // 2. Pendaftaran Aktif (Jadwal Aktif)
        $pendaftaranAktif = Pendaftaran::where('user_id', $userId)
                                    ->whereIn('status', ['Menunggu', 'Diperiksa Awal']) 
                                    ->whereDate('jadwal_dipilih', '>=', Carbon::today()) 
                                    ->with('jadwalPraktek.tenagaMedis') 
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        
        $allNotifications = collect();
        
        // Transform notifikasi DB
        foreach ($notificationsDb as $notif) {
            $data = $notif->data;
            
            $layanan = $data['layanan'] ?? 'N/A';
            $dokterName = $data['dokter_name'] ?? 'N/A';
            
            if (($layanan === 'N/A' || $dokterName === 'N/A') && isset($data['pendaftaran_id'])) {
                $pend = Pendaftaran::with('jadwalPraktek.tenagaMedis')->find($data['pendaftaran_id']);
                if ($pend) {
                    $layanan = $pend->nama_layanan;
                    $dokterName = $pend->jadwalPraktek->tenagaMedis->name ?? 'N/A';
                }
            }
            
            $allNotifications->push((object)[
                'id' => $notif->id,
                'type' => 'notification',
                'created_at' => $notif->created_at,
                'is_unread' => is_null($notif->read_at),
                'is_cancellation' => ($data['type'] ?? null) === 'Pembatalan Jadwal',
                'title' => $data['title'] ?? 'Pembaruan Umum',
                'message' => $data['message'] ?? 'Klik untuk detail lengkap.',
                'date' => $data['date'] ?? $notif->created_at->toDateString(),
                'no_antrian' => $data['no_antrian'] ?? '-',
                'estimasi_dilayani' => null, 
                'layanan' => $layanan,
                'dokter_name' => $dokterName,
                'raw_data' => $data,
            ]);
        }
        
        // Transform pendaftaran aktif
        foreach ($pendaftaranAktif as $pend) {
            $allNotifications->push((object)[
                'id' => 'pend_' . $pend->id,
                'type' => 'pendaftaran',
                'created_at' => $pend->created_at,
                'is_unread' => false,
                'is_cancellation' => false,
                'title' => 'Jadwal Konsultasi Aktif',
                'message' => 'Status: ' . $pend->status,
                'date' => $pend->jadwal_dipilih,
                'no_antrian' => $pend->no_antrian ?? '-',
                'estimasi_dilayani' => $pend->estimasi_dilayani, 
                'layanan' => $pend->nama_layanan,
                'dokter_name' => optional($pend->jadwalPraktek)->tenagaMedis->name ?? 'N/A',
                'raw_pendaftaran' => $pend,
            ]);
        }
        
        $allNotifications = $allNotifications->sortByDesc('created_at');
        
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $paginatedNotifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $allNotifications->forPage($currentPage, $perPage),
            $allNotifications->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('notifikasi_list', compact('paginatedNotifications'));
    }

    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }
    
    public function notifikasiDetail($pendaftaranId) { return redirect()->route('notifikasi.list'); }
    
    public function showProfile() { $user = Auth::user(); return view('profil', compact('user')); }
    public function editProfile() { $user = Auth::user(); return view('profil_edit', compact('user')); }
    
    public function updateProfile(Request $request) {
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date', 
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
        ]);
        $user->update($validatedData);
        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui!');
    }
    
    public function updatePhoto(Request $request) {
        $request->validate(['profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
        $user = Auth::user();
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $path = $request->file('profile_photo')->store('profil-photos', 'public'); 
        $user->profile_photo_path = $path;
        $user->save();
        return redirect()->route('profil')->with('success', 'Foto profil berhasil diperbarui!');
    }
    public function konfirmasiDatang($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        
        // Pastikan yang konfirmasi adalah pemilik akun
        if ($pendaftaran->user_id == Auth::id()) {
            $pendaftaran->status_panggilan = 'menunggu'; // Reset status agar notifikasi berhenti
            $pendaftaran->save();
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
}
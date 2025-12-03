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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Import yang kita perlukan untuk OTP
use App\Models\PasswordResetOtp;
use App\Notifications\PasienOtpNotification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // HAPUS 'use SendsPasswordResetEmails, ResetsPasswords;' DARI SINI

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

    /**
     * Menampilkan form untuk memasukkan email.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Membuat OTP, menyimpan, dan mengirimkannya ke email.
     */
    public function sendOtp(Request $request)
    {
        // 1. Validasi email
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->onlyInput('email');
        }

        // 2. Buat OTP
        $otp = random_int(100000, 999999); // 6 digit OTP

        // 3. Hapus OTP lama & simpan OTP baru
        PasswordResetOtp::updateOrCreate(
            ['email' => $user->email],
            [
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // 4. Kirim OTP ke email pasien
        try {
            $user->notify(new PasienOtpNotification($otp));
        } catch (\Exception $e) {
            // Tangani jika email gagal terkirim
            return back()->withErrors(['email' => 'Gagal mengirim email OTP. Coba lagi nanti.']);
        }

        // 5. Redirect ke halaman verifikasi OTP
        return redirect()->route('password.verify.form')
                         ->with('email', $user->email)
                         ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Menampilkan form untuk memasukkan OTP.
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Memverifikasi OTP yang dimasukkan.
     */
    public function verifyOtp(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        // 2. Cek OTP di database
        $otpData = PasswordResetOtp::where('email', $request->email)
                                 ->where('otp', $request->otp)
                                 ->first();
        
        // 3. Cek jika OTP tidak ada
        if (!$otpData) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.'])->withInput();
        }

        // 4. Cek jika OTP kedaluwarsa (1 menit)
        $expiredAt = Carbon::parse($otpData->created_at)->addMinutes(1);
        
        if (Carbon::now()->isAfter($expiredAt)) {
            $otpData->delete(); // Hapus OTP yang kedaluwarsa
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta lagi.'])->withInput();
        }

        // 5. ===== PERBAIKAN DI SINI =====
        // Gunakan $request->session()->put() dan paksa ->save()
        $request->session()->put('otp_verified_email', $request->email);
        $request->session()->save(); // Ini memaksa session ditulis ke database SEKARANG

        return redirect()->route('password.reset.form');
    }

    /**
     * Menampilkan form untuk ganti password baru (setelah OTP terverifikasi).
     */
    public function showResetPasswordForm(Request $request) // <-- Tambahkan Request
    {
        // ===== PERBAIKAN DI SINI =====
        // Baca session dari $request
        $email = $request->session()->get('otp_verified_email');

        if (!$email) {
            // Jika user belum verifikasi OTP, tendang ke form awal
            return redirect()->route('password.request')->withErrors(['email' => 'Harap verifikasi OTP Anda terlebih dahulu.']);
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Memperbarui password di database.
     */
    public function updatePassword(Request $request)
    {
        // 1. ===== PERBAIKAN DI SINI =====
        // Cek apakah user sudah verifikasi OTP
        $email = $request->session()->get('otp_verified_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah berakhir. Harap ulangi proses.']);
        }

        // 2. Validasi password baru
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        // 3. Update password user
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = $request->password; // Model akan hash otomatis
            $user->save();

            // 4. Hapus OTP & Session
            PasswordResetOtp::where('email', $email)->delete();
            $request->session()->forget('otp_verified_email');
            $request->session()->save(); // Paksa simpan setelah dihapus

            // 5. Redirect ke Login
            return redirect()->route('login')->with('success', 'Password Anda telah berhasil diperbarui. Silakan login.');
        }

        return redirect()->route('password.request')->withErrors(['email' => 'Terjadi kesalahan.']);
    }


    /*
    |--------------------------------------------------------------------------
    | FUNGSI PANEL PASIEN (Setelah Login)
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $user = Auth::user();
        $pasienId = $user->id;

        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');
        $jadwalHariIni = JadwalPraktek::with('tenagaMedis')
                                     ->whereJsonContains('hari', $namaHariIni)
                                     ->get();
        $notifikasiHariIni = Pendaftaran::where('user_id', $user->id)
                                        ->whereDate('jadwal_dipilih', Carbon::today())
                                        ->where('status', '!=', 'Selesai')
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

    public function notifikasiList()
    {
        $pasienId = Auth::id();
        
        $pendaftarans = Pendaftaran::where('user_id', $pasienId)
                                    ->where('status', '!=', 'Selesai') 
                                    ->whereDate('jadwal_dipilih', '>=', Carbon::today()) 
                                    ->with('jadwalPraktek.tenagaMedis') 
                                    ->orderBy('jadwal_dipilih', 'asc')
                                    ->get();

        return view('notifikasi_list', compact('pendaftarans'));
    }
    
    public function notifikasiDetail(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }
        return view('notifikasi_detail', compact('pendaftaran'));
    }
    
    public function showProfile()
    {
        $user = Auth::user();
        return view('profil', compact('user'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('profil_edit', compact('user')); 
    }

    public function updateProfile(Request $request)
    {
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

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $user = Auth::user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('profile_photo')->store('profil-photos', 'public'); 

        $user->profile_photo_path = $path;
        $user->save();

        return redirect()->route('profil')->with('success', 'Foto profil berhasil diperbarui!');
    }
}
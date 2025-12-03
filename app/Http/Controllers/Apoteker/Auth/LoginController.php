<?php

namespace App\Http\Controllers\Apoteker\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

// ===== PERBAIKAN: TAMBAHKAN 2 BARIS 'USE' STATEMENT INI ===== (Updated for correct namespace)
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// ========================================================

class LoginController extends Controller
{
    use AuthorizesRequests, CanResetPassword;

    /**
     * Menampilkan form login apoteker.
     */
    public function showLoginForm()
    {
        return view('apoteker.auth.login');
    }

    /**
     * Menangani proses login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Coba lakukan login menggunakan guard 'apoteker'
        if (! Auth::guard('apoteker')->attempt(
                $request->only('email', 'password'), 
                $request->filled('remember')
            )) {
            // Jika gagal
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        // 3. Jika berhasil
        $request->session()->regenerate();

        // Redirect ke dashboard apoteker
        return redirect()->intended(route('apoteker.dashboard'));
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('apoteker')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Redirect ke halaman utama
    }

    
    // ===== FUNGSI UNTUK LUPA PASSWORD =====

    /**
     * Tampilkan form permintaan link reset.
     * (Menimpa trait untuk menunjuk ke view yang benar)
     */
    public function showLinkRequestForm()
    {
        return view('apoteker.auth.email');
    }

    /**
     * Tampilkan form reset password.
     * (Menimpa trait untuk menunjuk ke view yang benar)
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('apoteker.auth.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Tentukan guard yang digunakan untuk reset password.
     */
    protected function guard()
    {
        return Auth::guard('apoteker');
    }

    /**
     * Tentukan broker password yang digunakan.
     */
    protected function broker()
    {
        return \Illuminate\Support\Facades\Password::broker('apotekers');
    }
}
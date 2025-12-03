<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user data dari Google
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user dengan Google ID ini sudah ada
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Jika sudah ada, langsung login
                Auth::login($user);
            } else {
                // Cek apakah email sudah terdaftar
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Update user yang sudah ada dengan Google ID
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                    Auth::login($existingUser);
                } else {
                    // Buat user baru
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'password' => bcrypt(uniqid()), // Random password
                    ]);
                    Auth::login($newUser);
                }
            }

            // Redirect sesuai role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal: ' . $e->getMessage());
        }
    }
}

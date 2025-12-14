<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran; 
use App\Models\User; // Tambahkan Model User

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // ==========================================
        // 1. NOTIFIKASI UNTUK ADMIN (SIDEBAR)
        // ==========================================
        View::composer('layouts.admin', function ($view) {
            $countPendaftaran = 0;
            $countUserBaru = 0;

            try {
                // --- A. NOTIF PENDAFTARAN (Antrian) ---
                if (Schema::hasTable('pendaftarans')) {
                    $query = Pendaftaran::whereDate('created_at', now())
                                ->where('status', 'Menunggu');
                    
                    if (session()->has('last_seen_pendaftaran')) {
                        $query->where('created_at', '>', session('last_seen_pendaftaran'));
                    }
                    $countPendaftaran = $query->count();
                }

                // --- B. NOTIF USER BARU (Register) ---
                if (Schema::hasTable('users')) {
                    // Cari user role 'pasien' atau null yang daftar HARI INI
                    $queryUser = User::where(function($q) {
                                    $q->where('role', 'pasien')->orWhereNull('role');
                                })
                                ->whereDate('created_at', now());

                    if (session()->has('last_seen_new_patient')) {
                        $queryUser->where('created_at', '>', session('last_seen_new_patient'));
                    }
                    $countUserBaru = $queryUser->count();
                }

            } catch (\Exception $e) { 
                $countPendaftaran = 0; 
                $countUserBaru = 0;
            }

            // Kirim kedua variabel ke view
            $view->with([
                'notifPasienBaru' => $countPendaftaran,
                'notifUserBaru'   => $countUserBaru
            ]);
        });

        // ==========================================
        // 2. NOTIFIKASI UNTUK PASIEN
        // ==========================================
        View::composer('layouts.main', function ($view) {
            $notifUser = 0;
            try {
                if (Auth::check() && Auth::user()->role === 'pasien') {
                    $query = Pendaftaran::where('user_id', Auth::id());

                    if (session()->has('last_seen_notif_user')) {
                        $query->where('updated_at', '>', session('last_seen_notif_user'));
                    }
                    $notifUser = $query->count();
                }
            } catch (\Exception $e) {
                $notifUser = 0;
            }

            $view->with('notifJadwal', $notifUser);
        });
    }
}
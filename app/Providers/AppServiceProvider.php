<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran; 

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
        // 1. NOTIFIKASI UNTUK ADMIN (Daftar Pasien)
        // ==========================================
        View::composer('layouts.admin', function ($view) {
            $count = 0;
            try {
                if (Schema::hasTable('pendaftarans')) {
                    $query = Pendaftaran::whereDate('created_at', now())
                                ->where('status', 'Menunggu');
                    
                    if (session()->has('last_seen_pendaftaran')) {
                        $query->where('created_at', '>', session('last_seen_pendaftaran'));
                    }
                    $count = $query->count();
                }
            } catch (\Exception $e) { $count = 0; }
            $view->with('notifPasienBaru', $count);
        });

        // ==========================================
        // 2. NOTIFIKASI UNTUK PASIEN (Jadwal/Notifikasi)
        // ==========================================
        View::composer('layouts.main', function ($view) {
            $notifUser = 0;
            try {
                // Pastikan user sedang login & role-nya pasien
                if (Auth::check() && Auth::user()->role === 'pasien') {
                    
                    // Ambil semua pendaftaran user ini
                    $query = Pendaftaran::where('user_id', Auth::id());

                    // LOGIKA UTAMA:
                    // Hanya hitung data yang dibuat/diupdate SETELAH terakhir kali menu dibuka
                    if (session()->has('last_seen_notif_user')) {
                        // Kita pakai updated_at agar jika status berubah (misal dipanggil), notif muncul lagi
                        $query->where('updated_at', '>', session('last_seen_notif_user'));
                    }

                    $notifUser = $query->count();
                }
            } catch (\Exception $e) {
                $notifUser = 0;
            }

            // Kirim variabel ke view
            $view->with('notifJadwal', $notifUser);
        });
    }
}
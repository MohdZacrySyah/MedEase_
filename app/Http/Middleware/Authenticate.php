<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request; // <-- Pastikan ini ada

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request tidak mengharapkan respons JSON
        if (! $request->expectsJson()) {
            
            // PERUBAHAN DIMULAI DI SINI
            
            // Jika URL yang diakses berawalan 'admin/'
            if ($request->is('admin/*')) {
                // Arahkan ke halaman login admin
                return route('admin.login');
            }

            // Jika URL yang diakses berawalan 'tenaga-medis/'
            if ($request->is('tenaga-medis/*')) {
                // Arahkan ke halaman login tenaga medis
                return route('tenaga-medis.login');
            }
            
            // ===================================================
            // TAMBAHAN UNTUK APOTEKER
            // ===================================================
            // Jika URL yang diakses berawalan 'apoteker/'
            if ($request->is('apoteker/*')) {
                // Arahkan ke halaman login apoteker
                return route('apoteker.login');
            }
            // ===================================================
            
            
            // Jika tidak semuanya, arahkan ke halaman login pasien (default)
            return route('login');
        }
        
        // Return null jika request mengharapkan JSON (untuk API)
        return null;
    }
}
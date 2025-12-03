<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Dapatkan user yang sedang login (dari guard manapun)
        $user = Auth::user();

        // 2. Jika tidak ada user ATAU role user tidak ada di dalam daftar $roles yang diizinkan
        if (!$user || !in_array($user->role, $roles)) {
            // "Tendang" user ke halaman login pasien sebagai default
            return redirect('/login');
        }

        // 3. Jika lolos, lanjutkan ke halaman yang dituju
        return $next($request);
    }
}
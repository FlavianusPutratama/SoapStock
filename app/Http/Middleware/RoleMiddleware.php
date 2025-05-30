<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  // Ini akan menangkap semua argumen peran yang diberikan
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            // Jika belum login, arahkan ke halaman login
            return redirect('login');
        }

        // 2. Dapatkan user yang sedang login
        $user = Auth::user();

        // 3. Cek apakah peran user ada di dalam daftar $roles yang diizinkan
        foreach ($roles as $role) {
            if ($user->role == $role) {
                // Jika peran user cocok, lanjutkan request ke controller/halaman berikutnya
                return $next($request);
            }
        }

        // 4. Jika peran user tidak ada dalam daftar yang diizinkan,
        // tampilkan halaman error 403 (Forbidden)
        // Anda bisa membuat view khusus untuk error 403 jika mau: resources/views/errors/403.blade.php
        abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK HALAMAN INI.');

        // Alternatif: Redirect ke halaman tertentu dengan pesan error
        // return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}
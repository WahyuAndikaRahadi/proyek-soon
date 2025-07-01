<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Penting untuk cek status login

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  // Parameter ini menangkap peran yang diizinkan (e.g., 'admin', 'guru')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect('/login'); // Jika belum, arahkan ke halaman login
        }

        $user = Auth::user(); // Dapatkan data pengguna yang sedang login

        // 2. Periksa apakah peran pengguna ada di daftar peran yang diizinkan
        // `in_array($user->role, $roles)` akan mencari peran pengguna dalam array $roles
        if (!in_array($user->role, $roles)) {
            // Jika peran tidak sesuai, tampilkan error 403 (Forbidden)
            abort(403, 'Akses Dilarang. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // 3. Jika semua pemeriksaan berhasil, lanjutkan permintaan ke controller
        return $next($request);
    }
}
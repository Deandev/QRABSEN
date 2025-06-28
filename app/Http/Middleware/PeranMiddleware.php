<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PeranMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$peranYangDiizinkan): Response
    {
        // Jika pengguna belum login, arahkan ke halaman login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Cek apakah peran pengguna ada di daftar peran yang diizinkan
        if (in_array($user->peran, $peranYangDiizinkan)) {
            return $next($request); // Izinkan akses
        }

        // Jika peran tidak diizinkan, arahkan ke halaman yang tidak diizinkan atau kembali
        // Anda bisa membuat halaman error 403 (Unauthorized) atau mengarahkan ke dashboard utama
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        // Atau: abort(403, 'Unauthorized action.');
    }
}
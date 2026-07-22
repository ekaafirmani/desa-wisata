<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        //cek apakah pengguna sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        //cek apakah akun aktif
        if (!auth()->user()->aktif) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun kamu tidak aktif, Hubungi Admin']);
        }

        //cek apakah role pengguna sesuai
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Kamu tidak memilik akses ke halaman ini');
        }

        return $next($request);
    }
}

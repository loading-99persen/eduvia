<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Belum login
        if (!auth()->check()) {
            return redirect('/login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Bukan admin
        if (auth()->user()->id_role != 1) {
            return redirect('/beranda')
                ->with('error', 'Kamu tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}
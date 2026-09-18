<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Alur setelah registrasi:
 * Lengkapi Profile -> Onboarding Study Profile -> Masuk aplikasi.
 */
class LengkapiProfil
{
    /** Route yang tidak boleh ikut dialihkan supaya tidak terjadi loop. */
    protected array $bebas = [
        'user.profil',
        'user.profil.update',
        'user.onboarding',
        'user.onboarding.store',
        'logout',
        'tentang',
        'kontak',
        'notifikasi.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        foreach ($this->bebas as $nama) {
            if ($request->routeIs($nama)) {
                return $next($request);
            }
        }

        if (!$user->profilLengkap()) {
            return redirect()->route('user.profil')
                ->with('error', 'Lengkapi profil terlebih dahulu sebelum memakai fitur lain.');
        }

        if (!$user->onboardingSelesai()) {
            return redirect()->route('user.onboarding')
                ->with('error', 'Pilih minat belajarmu dulu supaya rekomendasi bisa disusun.');
        }

        return $next($request);
    }
}

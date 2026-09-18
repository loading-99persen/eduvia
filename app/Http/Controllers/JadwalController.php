<?php

namespace App\Http\Controllers;

use App\Models\Webinar;

class JadwalController extends Controller
{
    /** Webinar yang sudah didaftarkan user. */
    public function index()
    {
        $user = auth()->user();

        $semua = Webinar::with(['komunitas', 'leader.profil'])
            ->whereHas('partisipasi', fn ($q) => $q->where('id_user', $user->id_user))
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $jadwal = $semua
            ->filter(fn (Webinar $w) => !$w->sudahLewat() && $w->status !== 'dibatalkan')
            ->sortBy(fn (Webinar $w) => $w->mulai->timestamp)
            ->values();

        $riwayat = $semua
            ->filter(fn (Webinar $w) => $w->sudahLewat() || $w->status === 'dibatalkan')
            ->sortByDesc(fn (Webinar $w) => $w->mulai->timestamp)
            ->values();

        return view('user.jadwal', compact('jadwal', 'riwayat'));
    }
}

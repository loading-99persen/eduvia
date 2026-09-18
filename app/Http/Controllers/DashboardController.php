<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Like;
use App\Models\PartisipasiWebinar;
use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $id   = $user->id_user;

        $idPostSaya = Post::where('id_user', $id)->pluck('id_post');

        $statistik = (object) [
            'komunitas' => $user->komunitas()->count(),
            'webinar'   => $user->partisipasiWebinar()->count(),
            'postingan' => $idPostSaya->count(),
            'interaksi' => Like::whereIn('id_post', $idPostSaya)->count()
                + Komentar::whereIn('id_post', $idPostSaya)->where('id_user', '!=', $id)->count(),
        ];

        // Grafik 7 hari terakhir: postingan + komentar + like yang dibuat user.
        $mulai = now()->subDays(6)->startOfDay();

        $tanggalPost = Post::where('id_user', $id)
            ->where('dibuat_pada', '>=', $mulai)
            ->pluck('dibuat_pada');

        $tanggalKomen = Komentar::where('id_user', $id)
            ->where('dibuat_pada', '>=', $mulai)
            ->pluck('dibuat_pada');

        $tanggalLike = Like::where('id_user', $id)
            ->where('dibuat_pada', '>=', $mulai)
            ->pluck('dibuat_pada');

        $semua = collect()
            ->merge($tanggalPost)
            ->merge($tanggalKomen)
            ->merge($tanggalLike)
            ->filter()
            ->map(fn ($t) => \Carbon\Carbon::parse($t)->toDateString());

        $aktivitasMingguan = [];
        $labelHari = [];

        for ($i = 6; $i >= 0; $i--) {
            $hari = now()->subDays($i);
            $labelHari[] = $hari->locale('id')->isoFormat('ddd');
            $aktivitasMingguan[] = $semua->filter(fn ($t) => $t === $hari->toDateString())->count();
        }

        $aktivitas = $this->aktivitasTerbaru($user);

        return view('user.dashboard', compact(
            'statistik',
            'aktivitasMingguan',
            'labelHari',
            'aktivitas'
        ));
    }

    /** Gabungan aktivitas terbaru milik user, diurutkan dari yang paling baru. */
    protected function aktivitasTerbaru($user)
    {
        $item = collect();

        Post::with('komunitas')
            ->where('id_user', $user->id_user)
            ->orderByDesc('dibuat_pada')
            ->take(5)
            ->get()
            ->each(function ($p) use ($item) {
                $item->push((object) [
                    'teks'   => 'Kamu memposting diskusi di ' . ($p->komunitas->nama_komunitas ?? 'komunitas'),
                    'waktu'  => $p->dibuat_pada,
                    'tautan' => route('post.show', $p->id_post),
                ]);
            });

        Komentar::with('post.komunitas')
            ->where('id_user', $user->id_user)
            ->orderByDesc('dibuat_pada')
            ->take(5)
            ->get()
            ->each(function ($k) use ($item) {
                $item->push((object) [
                    'teks'   => 'Kamu membalas diskusi di ' . ($k->post->komunitas->nama_komunitas ?? 'komunitas'),
                    'waktu'  => $k->dibuat_pada,
                    'tautan' => $k->post ? route('post.show', $k->id_post) : null,
                ]);
            });

        PartisipasiWebinar::with('webinar')
            ->where('id_user', $user->id_user)
            ->orderByDesc('bergabung_pada')
            ->take(5)
            ->get()
            ->each(function ($p) use ($item) {
                $item->push((object) [
                    'teks'   => 'Kamu terdaftar di webinar ' . ($p->webinar->judul ?? '-'),
                    'waktu'  => $p->bergabung_pada,
                    'tautan' => $p->webinar ? route('webinar.show', $p->id_webinar) : null,
                ]);
            });

        $user->memberKomunitas()
            ->with('komunitas')
            ->orderByDesc('bergabung_pada')
            ->take(5)
            ->get()
            ->each(function ($m) use ($item) {
                $item->push((object) [
                    'teks'   => 'Kamu bergabung dengan komunitas ' . ($m->komunitas->nama_komunitas ?? '-'),
                    'waktu'  => $m->bergabung_pada,
                    'tautan' => $m->komunitas ? route('komunitas.show', $m->id_komunitas) : null,
                ]);
            });

        return $item
            ->filter(fn ($i) => $i->waktu !== null)
            ->sortByDesc(fn ($i) => \Carbon\Carbon::parse($i->waktu)->timestamp)
            ->take(8)
            ->values();
    }
}

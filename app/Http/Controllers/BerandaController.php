<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use App\Models\Post;
use App\Models\Webinar;

class BerandaController extends Controller
{
    /**
     * Beranda: linimasa postingan yang disusun dari minat belajar,
     * komunitas yang diikuti, dan popularitas postingan.
     */
    public function index()
    {
        $user = auth()->user();

        $minat        = $user->minat()->get();
        $kataMinat    = $minat->pluck('nama_minat')->map(fn ($n) => mb_strtolower($n));
        $komunitasIds = $user->komunitas()->pluck('komunitas.id_komunitas')->all();

        $kandidat = Post::with(['user.profil', 'komunitas'])
            ->withCount(['likes', 'komentar'])
            ->withCount(['likes as disukai_count' => fn ($q) => $q->where('id_user', $user->id_user)])
            ->whereHas('komunitas', fn ($q) => $q->where('status', 'aktif'))
            ->orderByDesc('dibuat_pada')
            ->take(80)
            ->get();

        $posts = $kandidat
            ->sortByDesc(fn (Post $post) => $this->skor($post, $komunitasIds, $kataMinat))
            ->take(25)
            ->values();

        // Webinar terdekat yang relevan (komunitas diikuti atau sesuai minat).
        $webinarTerdekat = Webinar::with('komunitas')
            ->where('status', '!=', 'dibatalkan')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->take(12)
            ->get()
            ->sortByDesc(function (Webinar $w) use ($komunitasIds, $kataMinat) {
                $skor = in_array($w->id_komunitas, $komunitasIds) ? 20 : 0;
                $teks = mb_strtolower(($w->kategori ?? '') . ' ' . $w->judul);

                foreach ($kataMinat as $kata) {
                    if ($kata && str_contains($teks, $kata)) {
                        $skor += 10;
                        break;
                    }
                }

                return $skor;
            })
            ->take(3)
            ->values();

        $komunitasRekomendasi = Komunitas::aktif()
            ->withCount('memberKomunitas')
            ->when($komunitasIds, fn ($q) => $q->whereNotIn('id_komunitas', $komunitasIds))
            ->get()
            ->sortByDesc(function (Komunitas $k) use ($kataMinat) {
                $skor = $k->jumlah_member;
                $teks = mb_strtolower($k->kategori . ' ' . $k->nama_komunitas);

                foreach ($kataMinat as $kata) {
                    if ($kata && str_contains($teks, $kata)) {
                        $skor += 100;
                        break;
                    }
                }

                return $skor;
            })
            ->take(4)
            ->values();

        $komunitasSaya = $user->komunitas()->orderBy('nama_komunitas')->get();

        return view('beranda', compact(
            'posts',
            'minat',
            'webinarTerdekat',
            'komunitasRekomendasi',
            'komunitasSaya'
        ));
    }

    /** Skor sederhana untuk mengurutkan rekomendasi postingan. */
    protected function skor(Post $post, array $komunitasIds, $kataMinat): float
    {
        $skor = 0;

        if (in_array($post->id_komunitas, $komunitasIds)) {
            $skor += 60;
        }

        if ($post->komunitas) {
            $teks = mb_strtolower($post->komunitas->kategori . ' ' . $post->komunitas->nama_komunitas);

            foreach ($kataMinat as $kata) {
                if ($kata && str_contains($teks, $kata)) {
                    $skor += 35;
                    break;
                }
            }
        }

        $skor += ($post->likes_count * 2) + ($post->komentar_count * 3);

        // Postingan baru diprioritaskan, bobotnya menurun dalam 48 jam.
        $umurJam = $post->dibuat_pada ? $post->dibuat_pada->diffInHours(now()) : 999;
        $skor += max(0, 48 - $umurJam);

        return $skor;
    }
}

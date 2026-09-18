<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use App\Models\MemberKomunitas;
use App\Models\Post;
use App\Support\Notif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KomunitasController extends Controller
{
    /** Jelajahi komunitas: pencarian + filter kategori. */
    public function index(Request $request)
    {
        $cari     = trim((string) $request->query('cari', ''));
        $kategori = $request->query('kategori', 'Semua');

        $komunitas = Komunitas::aktif()
            ->with('leader.profil')
            ->withCount('memberKomunitas')
            ->withCount(['memberKomunitas as gabung_count' => fn ($q) => $q->where('id_user', auth()->id())])
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('nama_komunitas', 'like', "%{$cari}%")
                        ->orWhere('deskripsi', 'like', "%{$cari}%")
                        ->orWhere('kategori', 'like', "%{$cari}%");
                });
            })
            ->when($kategori && $kategori !== 'Semua', fn ($q) => $q->where('kategori', $kategori))
            ->orderByDesc('member_komunitas_count')
            ->orderBy('nama_komunitas')
            ->paginate(12)
            ->withQueryString();

        $daftarKategori = collect(['Semua'])
            ->merge(
                Komunitas::aktif()
                    ->whereNotNull('kategori')
                    ->distinct()
                    ->orderBy('kategori')
                    ->pluck('kategori')
            )
            ->unique()
            ->values();

        return view('komunitas.index', [
            'komunitas'     => $komunitas,
            'kategori'      => $daftarKategori,
            'kategoriAktif' => $kategori,
            'cari'          => $cari,
        ]);
    }

    /** Halaman komunitas: tab diskusi, member, dan webinar. */
    public function show(Request $request, $id)
    {
        $komunitas = Komunitas::with('leader.profil')
            ->withCount(['memberKomunitas', 'posts'])
            ->findOrFail($id);

        $user        = auth()->user();
        $sudahGabung = $komunitas->sudah_gabung;
        $sayaLeader  = $komunitas->saya_leader;

        $tab = in_array($request->query('tab'), ['diskusi', 'member', 'webinar'])
            ? $request->query('tab')
            : 'diskusi';

        $posts = Post::with(['user.profil', 'komunitas'])
            ->withCount(['likes', 'komentar'])
            ->withCount(['likes as disukai_count' => fn ($q) => $q->where('id_user', $user->id_user)])
            ->where('id_komunitas', $komunitas->id_komunitas)
            ->orderByDesc('dibuat_pada')
            ->paginate(10, ['*'], 'diskusi')
            ->withQueryString();

        $anggota = MemberKomunitas::with('user.profil')
            ->where('id_komunitas', $komunitas->id_komunitas)
            ->orderByRaw("CASE WHEN role = 'leader' THEN 0 ELSE 1 END")
            ->orderBy('bergabung_pada')
            ->get();

        $webinarKomunitas = $komunitas->webinar()
            ->withCount('partisipasi')
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu')
            ->get();

        return view('komunitas.show', compact(
            'komunitas',
            'sudahGabung',
            'sayaLeader',
            'tab',
            'posts',
            'anggota',
            'webinarKomunitas'
        ));
    }

    /** Gabung komunitas. */
    public function gabung($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        if ($komunitas->status !== 'aktif') {
            return back()->with('error', 'Komunitas ini sedang tidak aktif.');
        }

        $sudah = MemberKomunitas::where('id_komunitas', $komunitas->id_komunitas)
            ->where('id_user', auth()->id())
            ->exists();

        if ($sudah) {
            return back()->with('error', 'Kamu sudah menjadi anggota komunitas ini.');
        }

        MemberKomunitas::create([
            'id_komunitas' => $komunitas->id_komunitas,
            'id_user'      => auth()->id(),
            'role'         => (int) $komunitas->id_leader === (int) auth()->id() ? 'leader' : 'member',
        ]);

        $komunitas->chatroomAtauBuat();

        Notif::kirim(
            auth()->id(),
            'aktivitas_komunitas',
            'Berhasil bergabung',
            'Kamu berhasil bergabung dengan ' . $komunitas->nama_komunitas . '. Mulai dengan menyapa di group chat.',
            route('komunitas.show', $komunitas->id_komunitas)
        );

        if ((int) $komunitas->id_leader !== (int) auth()->id()) {
            Notif::kirim(
                $komunitas->id_leader,
                'aktivitas_komunitas',
                'Anggota baru',
                auth()->user()->nama . ' bergabung dengan ' . $komunitas->nama_komunitas . '.',
                route('komunitas.show', $komunitas->id_komunitas) . '?tab=member'
            );
        }

        return back()->with('success', 'Berhasil bergabung ke komunitas.');
    }

    /** Keluar komunitas. */
    public function keluar($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        if ((int) $komunitas->id_leader === (int) auth()->id()) {
            return back()->with('error', 'Leader tidak dapat keluar dari komunitas yang dipimpinnya.');
        }

        MemberKomunitas::where('id_komunitas', $komunitas->id_komunitas)
            ->where('id_user', auth()->id())
            ->delete();

        return redirect()->route('user.komunitas')
            ->with('success', 'Kamu sudah keluar dari ' . $komunitas->nama_komunitas . '.');
    }

    /** Komunitas yang diikuti user. */
    public function saya()
    {
        $user = auth()->user();

        $komunitasSaya = $user->komunitas()
            ->withCount('memberKomunitas')
            ->withCount(['posts as diskusi_baru' => fn ($q) => $q->where('dibuat_pada', '>=', now()->subDays(7))])
            ->orderBy('nama_komunitas')
            ->get();

        return view('user.komunitas', compact('komunitasSaya'));
    }

    /** Form pengaturan komunitas (khusus leader). */
    public function edit($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        abort_unless($komunitas->saya_leader || auth()->user()->isAdmin(), 403);

        return view('komunitas.edit', compact('komunitas'));
    }

    public function update(Request $request, $id)
    {
        $komunitas = Komunitas::findOrFail($id);

        abort_unless($komunitas->saya_leader || auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nama_komunitas' => ['required', 'string', 'max:100'],
            'deskripsi'      => ['required', 'string', 'max:2000'],
            'kategori'       => ['nullable', 'string', 'max:50'],
            'gambar'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('gambar')) {
            if ($komunitas->gambar && Storage::disk('public')->exists($komunitas->gambar)) {
                Storage::disk('public')->delete($komunitas->gambar);
            }

            $data['gambar'] = $request->file('gambar')->store('komunitas', 'public');
        } else {
            unset($data['gambar']);
        }

        $komunitas->update($data);

        return redirect()->route('komunitas.show', $komunitas->id_komunitas)
            ->with('success', 'Komunitas berhasil diperbarui.');
    }
}

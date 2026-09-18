<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Komunitas;
use App\Models\Post;
use App\Support\Notif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /** Simpan postingan baru. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_komunitas' => ['required', 'integer', 'exists:komunitas,id_komunitas'],
            'konten'       => ['required', 'string', 'max:5000'],
            'gambar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'file'         => ['nullable', 'file', 'max:5120'],
        ], [
            'id_komunitas.required' => 'Pilih komunitas tujuan terlebih dahulu.',
            'konten.required'       => 'Isi postingan tidak boleh kosong.',
        ]);

        $komunitas = Komunitas::findOrFail($data['id_komunitas']);

        if (!$komunitas->sudah_gabung) {
            return back()
                ->withInput()
                ->with('error', 'Kamu harus bergabung ke komunitas itu sebelum memposting.');
        }

        $post = new Post([
            'id_user'      => auth()->id(),
            'id_komunitas' => $komunitas->id_komunitas,
            'konten'       => $data['konten'],
        ]);

        if ($request->hasFile('gambar')) {
            $post->gambar = $request->file('gambar')->store('post', 'public');
        }

        if ($request->hasFile('file')) {
            $post->file = $request->file('file')->store('post-file', 'public');
        }

        $post->save();

        return redirect()
            ->to($request->input('kembali', route('komunitas.show', $komunitas->id_komunitas)))
            ->with('success', 'Postingan berhasil dibuat.');
    }

    /** Detail postingan beserta komentar dan balasannya. */
    public function show($id)
    {
        $post = Post::with(['user.profil', 'komunitas'])
            ->withCount(['likes', 'komentar'])
            ->withCount(['likes as disukai_count' => fn ($q) => $q->where('id_user', auth()->id())])
            ->findOrFail($id);

        $komentar = Komentar::with(['user.profil', 'balasan.user.profil'])
            ->where('id_post', $post->id_post)
            ->whereNull('id_parent')
            ->orderBy('dibuat_pada')
            ->get();

        return view('post.show', compact('post', 'komentar'));
    }

    public function edit($id)
    {
        $post = Post::with('komunitas')->findOrFail($id);

        abort_unless($post->milikSaya() || auth()->user()->isAdmin(), 403);

        return view('post.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        abort_unless($post->milikSaya() || auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'konten'       => ['required', 'string', 'max:5000'],
            'gambar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_gambar' => ['nullable', 'boolean'],
        ]);

        $post->konten = $data['konten'];

        if ($request->boolean('hapus_gambar') && $post->gambar) {
            Storage::disk('public')->delete($post->gambar);
            $post->gambar = null;
        }

        if ($request->hasFile('gambar')) {
            if ($post->gambar) {
                Storage::disk('public')->delete($post->gambar);
            }

            $post->gambar = $request->file('gambar')->store('post', 'public');
        }

        $post->diperbarui_pada = now();
        $post->save();

        return redirect()->route('post.show', $post->id_post)
            ->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        abort_unless($post->milikSaya() || auth()->user()->isAdmin(), 403);

        $idKomunitas = $post->id_komunitas;

        foreach (array_filter([$post->gambar, $post->file]) as $berkas) {
            if (Storage::disk('public')->exists($berkas)) {
                Storage::disk('public')->delete($berkas);
            }
        }

        $post->delete();

        return redirect()->route('komunitas.show', $idKomunitas)
            ->with('success', 'Postingan berhasil dihapus.');
    }

    /** Like / batal like. */
    public function like(Request $request, $id)
    {
        $post = Post::with('komunitas')->findOrFail($id);

        $like = $post->likes()->where('id_user', auth()->id())->first();

        if ($like) {
            $like->delete();
            $disukai = false;
        } else {
            $post->likes()->create(['id_user' => auth()->id()]);
            $disukai = true;

            if ((int) $post->id_user !== (int) auth()->id()) {
                Notif::kirim(
                    $post->id_user,
                    'like',
                    'Postinganmu disukai',
                    auth()->user()->nama . ' menyukai postinganmu: "' . Str::limit($post->konten, 60) . '"',
                    route('post.show', $post->id_post)
                );
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'disukai' => $disukai,
                'jumlah'  => $post->likes()->count(),
            ]);
        }

        return back();
    }

    /** Komentar atau balasan komentar. */
    public function komentar(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'komentar'  => ['required', 'string', 'max:2000'],
            'id_parent' => ['nullable', 'integer', 'exists:komentar,id_komen'],
        ], [
            'komentar.required' => 'Tulis dulu balasanmu.',
        ]);

        $komentar = Komentar::create([
            'id_post'   => $post->id_post,
            'id_user'   => auth()->id(),
            'id_parent' => $data['id_parent'] ?? null,
            'komentar'  => $data['komentar'],
        ]);

        // Notifikasi ke pemilik postingan.
        if ((int) $post->id_user !== (int) auth()->id()) {
            Notif::kirim(
                $post->id_user,
                'balasan_postingan',
                auth()->user()->nama . ' membalas postinganmu',
                Str::limit($komentar->komentar, 90),
                route('post.show', $post->id_post)
            );
        }

        // Notifikasi ke pemilik komentar yang dibalas.
        if ($komentar->id_parent) {
            $induk = Komentar::find($komentar->id_parent);

            if ($induk && (int) $induk->id_user !== (int) auth()->id() && (int) $induk->id_user !== (int) $post->id_user) {
                Notif::kirim(
                    $induk->id_user,
                    'balasan_postingan',
                    auth()->user()->nama . ' membalas komentarmu',
                    Str::limit($komentar->komentar, 90),
                    route('post.show', $post->id_post)
                );
            }
        }

        return back()->with('success', 'Balasan terkirim.');
    }

    public function hapusKomentar($id)
    {
        $komentar = Komentar::findOrFail($id);

        abort_unless($komentar->milikSaya() || auth()->user()->isAdmin(), 403);

        $idPost = $komentar->id_post;
        $komentar->delete();

        return redirect()->route('post.show', $idPost)
            ->with('success', 'Komentar dihapus.');
    }
}

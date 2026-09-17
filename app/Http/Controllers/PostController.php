<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Semua postingan
    public function index()
    {
        $posts = Post::with([
            'user.profil',
            'komunitas',
            'komentar',
            'likes'
        ])->latest()->get();

        return view('post.index', compact('posts'));
    }

    // Form buat post
    public function create()
    {
        return view('post.create');
    }

    // Simpan post
    public function store(Request $request)
    {
        $request->validate([
            'id_komunitas' => 'required',
            'isi' => 'required'
        ]);

        Post::create([
            'id_user' => Auth::id(),
            'id_komunitas' => $request->id_komunitas,
            'isi' => $request->isi
        ]);

        return redirect('/beranda')
            ->with('success', 'Postingan berhasil dibuat.');
    }

    // Detail post
    public function show($id)
    {
        $post = Post::with([
            'user.profil',
            'komunitas',
            'komentar.user.profil',
            'likes'
        ])->findOrFail($id);

        return view('post.show', compact('post'));
    }

    // Form edit
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return view('post.edit', compact('post'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'isi' => 'required'
        ]);

        $post->update([
            'isi' => $request->isi
        ]);

        return redirect('/beranda')
            ->with('success', 'Postingan berhasil diperbarui.');
    }

    // Hapus
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $post->delete();

        return redirect('/beranda')
            ->with('success', 'Postingan berhasil dihapus.');
    }

    // Like / unlike
    public function like($id)
    {
        $post = Post::findOrFail($id);

        $like = $post->likes()
            ->where('id_user', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create([
                'id_user' => Auth::id()
            ]);
        }

        return back();
    }

    // Komentar
    public function komentar(Request $request, $id)
    {
        $request->validate([
            'isi' => 'required'
        ]);

        $post = Post::findOrFail($id);

        $post->komentar()->create([
            'id_user' => Auth::id(),
            'isi' => $request->isi
        ]);

        return back();
    }
}
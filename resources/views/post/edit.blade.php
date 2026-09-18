@extends('layouts.user')

@section('title', 'Ubah postingan — Eduvia')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Ubah postingan</h1>
        <p class="mt-3 text-[15px] text-muted">
            Di komunitas {{ $post->komunitas->nama_komunitas ?? '-' }}
        </p>
    </header>

    <form action="{{ route('post.update', $post->id_post) }}" method="POST" enctype="multipart/form-data"
          class="rounded-4xl bg-white p-7">
        @csrf @method('PUT')

        <label for="konten" class="mb-1.5 block text-sm font-semibold">Isi postingan</label>
        <textarea id="konten" name="konten" rows="6" required
                  class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('konten', $post->konten) }}</textarea>

        @if ($post->gambar)
            <div class="mt-5">
                <img src="{{ asset('storage/' . $post->gambar) }}" alt="" class="w-full max-w-sm rounded-3xl object-cover">
                <label class="mt-3 flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="hapus_gambar" value="1" class="rounded border-line text-forest focus:ring-forest">
                    Hapus gambar ini
                </label>
            </div>
        @endif

        <div class="mt-5">
            <label for="gambar" class="mb-1.5 block text-sm font-semibold">Ganti / tambah gambar</label>
            <input id="gambar" type="file" name="gambar" accept="image/*"
                   class="block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-canvas file:px-5 file:py-2.5 file:text-sm file:font-semibold hover:file:bg-line">
        </div>

        <div class="mt-7 flex flex-wrap items-center gap-3 border-t border-line pt-6">
            <button class="rounded-full bg-forest px-7 py-3 text-sm font-semibold text-white hover:bg-forestdim">Simpan perubahan</button>
            <a href="{{ route('post.show', $post->id_post) }}" class="text-sm font-semibold text-muted hover:text-ink">Batal</a>
        </div>
    </form>
@endsection

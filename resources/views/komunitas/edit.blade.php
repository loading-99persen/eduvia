@extends('layouts.user')

@section('title', 'Kelola komunitas — Eduvia')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Kelola komunitas</h1>
        <p class="mt-3 max-w-[52ch] text-[15px] leading-relaxed text-muted">
            Perbarui identitas komunitas yang kamu pimpin.
        </p>
    </header>

    <form action="{{ route('komunitas.update', $komunitas->id_komunitas) }}" method="POST"
          enctype="multipart/form-data" class="rounded-4xl bg-white p-7">
        @csrf @method('PUT')

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="nama_komunitas" class="mb-1.5 block text-sm font-semibold">Nama komunitas</label>
                <input id="nama_komunitas" name="nama_komunitas" required
                       value="{{ old('nama_komunitas', $komunitas->nama_komunitas) }}"
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            </div>

            <div>
                <label for="kategori" class="mb-1.5 block text-sm font-semibold">Kategori</label>
                <input id="kategori" name="kategori" value="{{ old('kategori', $komunitas->kategori) }}"
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            </div>

            <div>
                <label for="gambar" class="mb-1.5 block text-sm font-semibold">Logo atau banner</label>
                <input id="gambar" type="file" name="gambar" accept="image/*"
                       class="block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-canvas file:px-5 file:py-2.5 file:text-sm file:font-semibold hover:file:bg-line">
            </div>

            <div class="sm:col-span-2">
                <label for="deskripsi" class="mb-1.5 block text-sm font-semibold">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required
                          class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('deskripsi', $komunitas->deskripsi) }}</textarea>
            </div>
        </div>

        <div class="mt-7 flex flex-wrap items-center gap-3 border-t border-line pt-6">
            <button class="rounded-full bg-forest px-7 py-3 text-sm font-semibold text-white hover:bg-forestdim">Simpan perubahan</button>
            <a href="{{ route('komunitas.show', $komunitas->id_komunitas) }}" class="text-sm font-semibold text-muted hover:text-ink">Batal</a>
        </div>
    </form>
@endsection

@extends('layouts.user')

@section('title', 'Jelajahi komunitas — Eduvia')

@php
    $warna = ['bg-lime', 'bg-sun', 'bg-tangerine', 'bg-lilac', 'bg-limesoft', 'bg-forest text-white'];
@endphp

@section('content')

    <section class="mb-6 rounded-4xl bg-white p-7 md:p-9">
        <h1 class="headline max-w-[16ch] text-[38px] font-extrabold md:text-[46px]">
            Temukan teman belajar
        </h1>
        <p class="mt-4 max-w-[54ch] text-[15px] leading-relaxed text-muted">
            Setiap komunitas punya ruang diskusi, anggota, group chat, dan webinar sendiri.
            Pilih yang sesuai dengan yang sedang kamu pelajari.
        </p>

        <form action="{{ route('komunitas.index') }}" method="GET" class="mt-6">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex min-w-[240px] flex-1 items-center gap-2 rounded-full bg-canvas px-5 py-3">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <circle cx="9" cy="9" r="6" stroke="#6E6880" stroke-width="2"/>
                        <path d="M13.5 13.5 18 18" stroke="#6E6880" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <label class="sr-only" for="cari">Cari komunitas</label>
                    <input id="cari" name="cari" value="{{ $cari }}"
                           placeholder="Cari nama komunitas, misalnya &ldquo;Laravel&rdquo;"
                           class="w-full bg-transparent text-sm placeholder:text-muted focus:outline-none">
                </div>
                <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
                <button class="rounded-full bg-forest px-6 py-3 text-sm font-semibold text-white hover:bg-forestdim">Cari</button>
                @if ($cari !== '' || $kategoriAktif !== 'Semua')
                    <a href="{{ route('komunitas.index') }}" class="text-sm font-semibold text-muted hover:text-ink">Atur ulang</a>
                @endif
            </div>
        </form>

        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($kategori as $kat)
                <a href="{{ route('komunitas.index', ['kategori' => $kat, 'cari' => $cari]) }}"
                   class="rounded-full px-4 py-2 text-sm font-semibold transition
                          {{ $kategoriAktif === $kat ? 'bg-forest text-white' : 'bg-canvas text-muted hover:text-ink' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>
    </section>

    <div class="mb-4 flex items-center gap-2">
        <h2 class="text-sm font-bold">{{ $komunitas->total() }} komunitas</h2>
        <span class="h-px flex-1 bg-line"></span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        @forelse ($komunitas as $i => $k)
            <article class="flex flex-col rounded-4xl bg-white p-6">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl {{ $warna[$i % count($warna)] }} text-sm font-extrabold">
                        {{ strtoupper(mb_substr($k->nama_komunitas, 0, 2)) }}
                    </span>
                    @if ($k->kategori)
                        <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">{{ $k->kategori }}</span>
                    @endif
                </div>

                <h3 class="mt-4 text-lg font-extrabold tracking-tight">{{ $k->nama_komunitas }}</h3>
                <p class="mt-2 max-w-[46ch] text-sm leading-relaxed text-muted">
                    {{ \Illuminate\Support\Str::limit($k->deskripsi, 140) }}
                </p>
                <p class="mt-2 text-xs text-muted">Leader: {{ $k->leader->profil->nama_lengkap ?? 'Belum ditentukan' }}</p>

                <div class="mt-5 flex items-center gap-3 border-t border-line pt-5">
                    <span class="text-sm font-semibold text-muted">{{ $k->member_komunitas_count }} anggota</span>

                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{ route('komunitas.show', $k->id_komunitas) }}"
                           class="rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">Lihat</a>

                        @if ($k->sudah_gabung)
                            <span class="rounded-full bg-limesoft px-4 py-2 text-sm font-semibold text-forest">Sudah gabung</span>
                        @else
                            <form action="{{ route('komunitas.gabung', $k->id_komunitas) }}" method="POST">
                                @csrf
                                <button class="rounded-full bg-forest px-4 py-2 text-sm font-semibold text-white hover:bg-forestdim">
                                    Gabung
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center sm:col-span-2">
                <p class="text-lg font-bold">Tidak ada komunitas yang cocok</p>
                <p class="mx-auto mt-2 max-w-[46ch] text-sm leading-relaxed text-muted">
                    Coba kata kunci lain, atau ajukan komunitas baru dengan topik yang kamu cari.
                </p>
                <a href="{{ route('pengajuan.create') }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                    Ajukan komunitas baru
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $komunitas->links() }}
    </div>
@endsection

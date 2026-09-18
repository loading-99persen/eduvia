@extends('layouts.user')

@section('title', 'Webinar — Eduvia')

@php
    $warna = ['bg-lime', 'bg-sun', 'bg-lilac', 'bg-tangerine'];

    $tabs = [
        'mendatang' => 'Akan datang',
        'komunitas' => 'Dari komunitas saya',
        'diikuti'   => 'Saya ikuti',
        'selesai'   => 'Sudah selesai',
    ];
@endphp

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Webinar komunitas</h1>
        <p class="mt-3 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Dibuat oleh leader komunitas dan ditinjau admin sebelum tayang. Sekali klik daftar,
            jadwalnya langsung tersimpan di akunmu.
        </p>
    </header>

    <div class="mb-5 rounded-4xl bg-white p-3">
        <div class="flex flex-wrap gap-1">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('webinar.index', ['filter' => $key, 'cari' => $cari]) }}"
                   class="rounded-full px-4 py-2.5 text-sm font-semibold transition
                          {{ $filter === $key ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('webinar.index') }}" method="GET" class="mt-3 flex flex-wrap items-center gap-2 border-t border-line pt-3">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <label class="sr-only" for="cari-webinar">Cari webinar</label>
            <input id="cari-webinar" name="cari" value="{{ $cari }}" placeholder="Cari judul atau kategori webinar"
                   class="min-w-[220px] flex-1 rounded-full border-0 bg-canvas px-5 py-2.5 text-sm focus:ring-2 focus:ring-forest">
            <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Cari</button>
        </form>
    </div>

    <div class="space-y-4">
        @forelse ($webinar as $i => $w)
            <article class="rounded-4xl bg-white p-6">
                <div class="flex flex-wrap items-start gap-6">

                    <div class="grid h-24 w-24 shrink-0 place-items-center rounded-3xl {{ $warna[$i % count($warna)] }} text-center leading-none">
                        <span>
                            <span class="block text-3xl font-extrabold">{{ $w->mulai->format('d') }}</span>
                            <span class="mt-1 block text-[11px] font-semibold">{{ $w->mulai->locale('id')->isoFormat('MMM Y') }}</span>
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($w->kategori)
                                <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">{{ $w->kategori }}</span>
                            @endif
                            <a href="{{ route('komunitas.show', $w->id_komunitas) }}" class="text-xs text-muted hover:underline">
                                {{ $w->komunitas->nama_komunitas ?? '' }}
                            </a>
                            <span class="rounded-full bg-limesoft px-3 py-1 text-xs font-semibold text-forest">{{ $w->label_status }}</span>
                        </div>

                        <h2 class="mt-2 text-xl font-extrabold tracking-tight">{{ $w->judul }}</h2>
                        <p class="mt-2 max-w-[60ch] text-sm leading-relaxed text-muted">
                            {{ \Illuminate\Support\Str::limit($w->deskripsi, 180) }}
                        </p>
                        <p class="mt-3 text-sm font-semibold text-forest">
                            {{ $w->mulai->locale('id')->isoFormat('dddd, D MMMM') }} &middot;
                            <span data-wib="{{ $w->mulai->format('Y-m-d H:i') }}">{{ $w->mulai->format('H:i') }} WIB</span> &middot;
                            {{ $w->pembicara ?: ($w->leader->profil->nama_lengkap ?? '-') }}
                            &middot; {{ $w->partisipasi_count }} peserta
                        </p>
                    </div>

                    <div class="flex w-full flex-wrap gap-2 sm:w-auto sm:flex-col">
                        <a href="{{ route('webinar.show', $w->id_webinar) }}"
                           class="rounded-full bg-canvas px-5 py-2.5 text-center text-sm font-semibold hover:bg-line">Detail</a>

                        @if ($w->sudah_daftar)
                            <span class="rounded-full bg-limesoft px-5 py-2.5 text-center text-sm font-semibold text-forest">Sudah terdaftar</span>
                        @elseif ($w->sudahLewat())
                            <span class="rounded-full bg-canvas px-5 py-2.5 text-center text-sm font-semibold text-muted">Sudah lewat</span>
                        @else
                            <form action="{{ route('webinar.ikut', $w->id_webinar) }}" method="POST">
                                @csrf
                                <button class="w-full rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                                    Daftar webinar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Belum ada webinar di daftar ini</p>
                <p class="mx-auto mt-2 max-w-[46ch] text-sm leading-relaxed text-muted">
                    Gabung komunitas dulu — webinar biasanya diumumkan lewat komunitas yang kamu ikuti.
                </p>
                <a href="{{ route('komunitas.index') }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Jelajahi komunitas</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $webinar->links() }}</div>
@endsection

@extends('layouts.user')

@section('title', 'Komunitas saya — Eduvia')

@php
    $warna = ['bg-lime', 'bg-sun', 'bg-tangerine', 'bg-lilac', 'bg-limesoft'];
@endphp

@section('content')

    <header class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Komunitas saya</h1>
            <p class="mt-3 max-w-[50ch] text-[15px] leading-relaxed text-muted">
                Semua komunitas yang kamu ikuti, lengkap dengan statusmu di masing-masing komunitas.
            </p>
        </div>
        <a href="{{ route('komunitas.index') }}"
           class="rounded-full bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forestdim">Cari komunitas lain</a>
    </header>

    <div class="grid gap-4 sm:grid-cols-2">
        @forelse ($komunitasSaya as $i => $k)
            @php $sayaLeader = ($k->pivot->role ?? 'member') === 'leader'; @endphp
            <article class="rounded-4xl bg-white p-6">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl {{ $warna[$i % count($warna)] }} text-sm font-extrabold">
                        {{ strtoupper(mb_substr($k->nama_komunitas, 0, 2)) }}
                    </span>
                    @if ($sayaLeader)
                        <span class="rounded-full bg-forest px-3 py-1 text-xs font-bold text-lime">Leader</span>
                    @else
                        <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">Member</span>
                    @endif
                </div>

                <h2 class="mt-4 text-lg font-extrabold tracking-tight">{{ $k->nama_komunitas }}</h2>
                <p class="mt-1 text-sm text-muted">
                    {{ $k->member_komunitas_count }} anggota
                    @if (($k->diskusi_baru ?? 0) > 0)
                        &middot; <span class="font-semibold text-forest">{{ $k->diskusi_baru }} diskusi baru minggu ini</span>
                    @endif
                </p>

                <div class="mt-5 flex flex-wrap gap-2 border-t border-line pt-5">
                    <a href="{{ route('komunitas.show', $k->id_komunitas) }}"
                       class="rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">Buka komunitas</a>
                    <a href="{{ route('komunitas.chat', $k->id_komunitas) }}"
                       class="rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">Group chat</a>
                    @if ($sayaLeader)
                        <a href="{{ route('pengajuan.create', ['tipe' => 'webinar', 'komunitas' => $k->id_komunitas]) }}"
                           class="rounded-full bg-forest px-4 py-2 text-sm font-semibold text-white hover:bg-forestdim">Ajukan webinar</a>
                    @else
                        <form action="{{ route('komunitas.keluar', $k->id_komunitas) }}" method="POST"
                              onsubmit="return confirm('Keluar dari komunitas ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-full px-4 py-2 text-sm font-semibold text-muted hover:text-ember">Keluar</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center sm:col-span-2">
                <p class="text-lg font-bold">Belum ada komunitas</p>
                <p class="mx-auto mt-2 max-w-[44ch] text-sm leading-relaxed text-muted">
                    Pilih satu komunitas yang paling dekat dengan minatmu, lalu ikuti diskusinya beberapa hari.
                </p>
                <a href="{{ route('komunitas.index') }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Jelajahi komunitas</a>
            </div>
        @endforelse
    </div>
@endsection

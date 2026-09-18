@extends('layouts.user')

@section('title', 'Notifikasi — Eduvia')

@php
    $ikon = [
        'balasan_postingan'   => ['bg-lime', 'Balasan'],
        'like'                => ['bg-tangerine', 'Suka'],
        'mention'             => ['bg-lilac', 'Sebutan'],
        'aktivitas_komunitas' => ['bg-limesoft', 'Komunitas'],
        'pengajuan'           => ['bg-sun', 'Pengajuan'],
        'webinar'             => ['bg-lilac', 'Webinar'],
        'jadwal'              => ['bg-lime', 'Jadwal'],
        'sistem'              => ['bg-canvas', 'Sistem'],
    ];
@endphp

@section('content')

    <header class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Notifikasi</h1>
            <p class="mt-3 text-[15px] text-muted">
                {{ $belum > 0 ? $belum . ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($belum > 0)
                <form action="{{ route('notifikasi.bacaSemua') }}" method="POST">
                    @csrf @method('PUT')
                    <button class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Tandai semua dibaca</button>
                </form>
            @endif

            @if ($notifikasi->total() > 0)
                <form action="{{ route('notifikasi.hapusSemua') }}" method="POST"
                      onsubmit="return confirm('Hapus semua notifikasi?')">
                    @csrf @method('DELETE')
                    <button class="rounded-full px-5 py-2.5 text-sm font-semibold text-muted hover:text-ember">Hapus semua</button>
                </form>
            @endif
        </div>
    </header>

    <div class="mb-5 flex gap-1 rounded-full bg-white p-1">
        @foreach (['semua' => 'Semua', 'belum' => 'Belum dibaca'] as $key => $label)
            <a href="{{ route('notifikasi.index', ['filter' => $key]) }}"
               class="flex-1 rounded-full px-4 py-2.5 text-center text-sm font-semibold transition
                      {{ $filter === $key ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($notifikasi as $n)
            @php [$warna, $labelTipe] = $ikon[$n->tipe] ?? $ikon['sistem']; @endphp

            <article class="rounded-4xl bg-white p-5 {{ $n->dibaca ? '' : 'ring-2 ring-forest/20' }}">
                <div class="flex items-start gap-4">
                    <span class="mt-0.5 grid h-10 w-10 shrink-0 place-items-center rounded-2xl {{ $warna }} text-[10px] font-bold uppercase">
                        {{ mb_substr($labelTipe, 0, 3) }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-bold">{{ $n->judul }}</h2>
                            @unless ($n->dibaca)
                                <span class="h-2 w-2 rounded-full bg-tangerine"></span>
                            @endunless
                            <span class="text-xs text-muted">&middot; {{ optional($n->dibuat_pada)->diffForHumans() }}</span>
                        </div>

                        <p class="mt-1.5 max-w-[64ch] text-sm leading-relaxed text-muted">{{ $n->isi }}</p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if ($n->tautan)
                                <a href="{{ route('notifikasi.buka', $n->id_notifikasi) }}"
                                   class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">Lihat</a>
                            @endif

                            @unless ($n->dibaca)
                                <form action="{{ route('notifikasi.baca', $n->id_notifikasi) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ink">Tandai dibaca</button>
                                </form>
                            @endunless

                            <form action="{{ route('notifikasi.destroy', $n->id_notifikasi) }}" method="POST" class="ml-auto">
                                @csrf @method('DELETE')
                                <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Tidak ada notifikasi</p>
                <p class="mx-auto mt-2 max-w-[44ch] text-sm leading-relaxed text-muted">
                    Balasan diskusi, pengajuan yang diproses, dan pengingat webinar akan muncul di sini.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifikasi->links() }}</div>
@endsection

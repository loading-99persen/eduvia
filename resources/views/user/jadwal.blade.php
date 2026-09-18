@extends('layouts.user')

@section('title', 'Jadwal saya — Eduvia')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Jadwal saya</h1>
        <p class="mt-3 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Webinar yang sudah kamu daftarkan muncul di sini. Tautan meeting terbuka
            {{ \App\Models\Webinar::MENIT_AKSES_AWAL }} menit sebelum waktu mulai.
        </p>
    </header>

    <div class="space-y-4">
        @forelse ($jadwal as $w)
            <article class="overflow-hidden rounded-4xl bg-white">
                <div class="flex flex-wrap items-center gap-6 p-6">

                    <div class="grid h-20 w-20 shrink-0 place-items-center rounded-3xl bg-forest text-center leading-none text-white">
                        <span>
                            <span class="block text-2xl font-extrabold">{{ $w->mulai->format('d') }}</span>
                            <span class="mt-1 block text-[11px] font-semibold text-lime">{{ $w->mulai->locale('id')->isoFormat('MMM') }}</span>
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-limesoft px-3 py-1 text-xs font-semibold text-forest">Terdaftar</span>
                            <span class="text-xs text-muted">{{ $w->komunitas->nama_komunitas ?? '-' }}</span>
                            @if ($w->sedangBerlangsung())
                                <span class="rounded-full bg-tangerine px-3 py-1 text-xs font-bold text-ink">Sedang berlangsung</span>
                            @endif
                        </div>
                        <h2 class="mt-2 text-xl font-extrabold tracking-tight">{{ $w->judul }}</h2>
                        <p class="mt-1 text-sm text-muted">
                            {{ $w->mulai->locale('id')->isoFormat('dddd, D MMMM Y') }} &middot;
                            <span data-wib="{{ $w->mulai->format('Y-m-d H:i') }}">{{ $w->mulai->format('H:i') }} WIB</span> &middot;
                            {{ $w->pembicara ?: ($w->leader->profil->nama_lengkap ?? '-') }}
                        </p>
                    </div>

                    <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
                        <a href="{{ route('webinar.show', $w->id_webinar) }}"
                           class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Detail</a>

                        @if ($w->bolehAksesLink())
                            <a href="{{ $w->link_meeting }}" target="_blank" rel="noopener"
                               class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                                Masuk meeting
                            </a>
                        @else
                            <span class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold text-muted">
                                Tautan terbuka saat waktunya
                            </span>
                        @endif

                        <form action="{{ route('webinar.batal', $w->id_webinar) }}" method="POST"
                              onsubmit="return confirm('Batalkan pendaftaran webinar ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-full px-4 py-2.5 text-sm font-semibold text-muted hover:text-ember">Batalkan</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Belum ada jadwal</p>
                <p class="mx-auto mt-2 max-w-[46ch] text-sm leading-relaxed text-muted">
                    Setiap webinar yang kamu ikuti langsung masuk ke halaman ini, tanpa perlu isi formulir lagi.
                </p>
                <a href="{{ route('webinar.index') }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Lihat webinar</a>
            </div>
        @endforelse
    </div>

    @if ($riwayat->isNotEmpty())
        <h2 class="mb-4 mt-10 text-sm font-bold">Sudah selesai</h2>
        <ul class="divide-y divide-line rounded-4xl bg-white px-6">
            @foreach ($riwayat as $r)
                <li class="flex flex-wrap items-center gap-3 py-4">
                    <a href="{{ route('webinar.show', $r->id_webinar) }}" class="text-sm font-semibold hover:underline">{{ $r->judul }}</a>
                    <span class="text-xs text-muted">{{ $r->komunitas->nama_komunitas ?? '-' }}</span>
                    @if ($r->status === 'dibatalkan')
                        <span class="rounded-full bg-tangerine/15 px-3 py-1 text-xs font-semibold text-ember">Dibatalkan</span>
                    @endif
                    <span class="ml-auto text-xs text-muted">{{ $r->mulai->locale('id')->isoFormat('D MMM Y') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endsection

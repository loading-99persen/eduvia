@extends('layouts.user')

@section('title', ($user->profil->nama_lengkap ?? 'Pengguna') . ' — Eduvia')

@php
    $profil = $user->profil;
    $warnaMinat = ['bg-tangerine', 'bg-lilac', 'bg-sun', 'bg-lime'];
@endphp

@section('content')

    <section class="mb-6 overflow-hidden rounded-4xl bg-white">
        <div class="h-28 bg-forest"></div>
        <div class="flex flex-wrap items-end gap-5 px-7 pb-7">
            <div class="-mt-12">
                @if ($profil?->photo)
                    <img src="{{ asset('storage/' . $profil->photo) }}" alt="" class="h-24 w-24 rounded-3xl border-4 border-white object-cover">
                @else
                    <span class="grid h-24 w-24 place-items-center rounded-3xl border-4 border-white bg-lime text-2xl font-extrabold">
                        {{ strtoupper(mb_substr($profil->nama_lengkap ?? 'P', 0, 1)) }}
                    </span>
                @endif
            </div>
            <div class="min-w-0 flex-1 pt-2">
                <h1 class="text-2xl font-extrabold tracking-tight">{{ $profil->nama_lengkap ?? 'Pengguna Eduvia' }}</h1>
                <p class="mt-1 text-sm text-muted">
                    {{ $profil->tingkat_pendidikan ?? '—' }} &middot; {{ $profil->institusi ?? '—' }}
                </p>
            </div>
            <a href="{{ route('report.create', ['tipe' => 'user', 'id' => $user->id_user, 'kembali' => url()->current()]) }}"
               class="rounded-full bg-canvas px-4 py-2 text-sm font-semibold text-muted hover:text-ember">Laporkan</a>
        </div>

        @if ($profil?->bio)
            <p class="max-w-[62ch] px-7 pb-7 text-[15px] leading-relaxed text-muted">{{ $profil->bio }}</p>
        @endif
    </section>

    <div class="grid gap-4 lg:grid-cols-[.9fr_1.1fr]">
        <section class="rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Minat belajar</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($user->minat as $i => $m)
                    <span class="rounded-full {{ $warnaMinat[$i % count($warnaMinat)] }} px-4 py-2 text-sm font-semibold">{{ $m->nama_minat }}</span>
                @empty
                    <p class="text-sm text-muted">Belum diisi.</p>
                @endforelse
            </div>

            <h2 class="mt-7 text-sm font-bold">Komunitas</h2>
            <ul class="mt-3 space-y-2">
                @forelse ($komunitas as $k)
                    <li>
                        <a href="{{ route('komunitas.show', $k->id_komunitas) }}"
                           class="flex items-center gap-3 rounded-2xl px-2 py-2 hover:bg-canvas">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-canvas text-xs font-bold">
                                {{ strtoupper(mb_substr($k->nama_komunitas, 0, 2)) }}
                            </span>
                            <span class="text-sm font-semibold">{{ $k->nama_komunitas }}</span>
                            <span class="ml-auto text-xs text-muted">{{ ($k->pivot->role ?? 'member') === 'leader' ? 'Leader' : 'Member' }}</span>
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-muted">Belum bergabung ke komunitas.</li>
                @endforelse
            </ul>
        </section>

        <section class="space-y-4">
            <h2 class="text-sm font-bold">Postingan terbaru</h2>
            @forelse ($posts as $post)
                @include('partials.post-card', ['post' => $post])
            @empty
                <div class="rounded-4xl bg-white p-8 text-center text-sm text-muted">Belum ada postingan.</div>
            @endforelse
        </section>
    </div>
@endsection

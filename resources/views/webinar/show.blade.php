@extends('layouts.user')

@section('title', $webinar->judul . ' — Eduvia')

@php
    $profil = auth()->user()->profil;
@endphp

@section('content')

    <section class="mb-6 overflow-hidden rounded-4xl bg-forest text-white">
        <div class="grid gap-8 p-7 md:grid-cols-[1.15fr_.85fr] md:p-9">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($webinar->kategori)
                        <span class="inline-flex rounded-full bg-lime px-3 py-1 text-xs font-bold text-ink">{{ $webinar->kategori }}</span>
                    @endif
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">{{ $webinar->label_status }}</span>
                </div>

                <h1 class="headline mt-4 text-[38px] font-extrabold md:text-[46px]">{{ $webinar->judul }}</h1>
                <p class="mt-4 text-sm text-white/75">
                    Diselenggarakan oleh
                    <a href="{{ route('komunitas.show', $webinar->id_komunitas) }}" class="font-semibold text-lime hover:underline">
                        {{ $webinar->komunitas->nama_komunitas ?? 'Komunitas' }}
                    </a>
                </p>

                <dl class="mt-7 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-3xl bg-white/10 p-4">
                        <dt class="text-xs text-white/70">Tanggal</dt>
                        <dd class="mt-1 text-sm font-bold">{{ $webinar->mulai->locale('id')->isoFormat('D MMM Y') }}</dd>
                    </div>
                    <div class="rounded-3xl bg-white/10 p-4">
                        <dt class="text-xs text-white/70">Waktu</dt>
                        <dd class="mt-1 text-sm font-bold" data-wib="{{ $webinar->mulai->format('Y-m-d H:i') }}">{{ $webinar->mulai->format('H:i') }} WIB</dd>
                    </div>
                    <div class="rounded-3xl bg-white/10 p-4">
                        <dt class="text-xs text-white/70">Peserta</dt>
                        <dd class="mt-1 text-sm font-bold">{{ $jumlahPeserta }} orang</dd>
                    </div>
                </dl>
            </div>

            {{-- Kartu pendaftaran --}}
            <div class="self-start rounded-3xl bg-white p-6 text-ink">
                @if ($sudahDaftar)
                    <p class="text-base font-extrabold">Kamu sudah terdaftar</p>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        Webinar ini ada di halaman Jadwal. Tautan meeting terbuka
                        {{ \App\Models\Webinar::MENIT_AKSES_AWAL }} menit sebelum mulai.
                    </p>

                    @if ($aksesLink)
                        <a href="{{ $webinar->link_meeting }}" target="_blank" rel="noopener"
                           class="mt-5 block rounded-full bg-forest px-5 py-3 text-center text-sm font-semibold text-white hover:bg-forestdim">
                            Masuk meeting
                        </a>
                    @else
                        <div class="mt-5 rounded-2xl bg-canvas px-5 py-4 text-sm leading-relaxed text-muted">
                            Tautan meeting terbuka mendekati waktu mulai, dan hanya untuk peserta terdaftar.
                        </div>
                    @endif

                    <a href="{{ route('user.jadwal') }}" class="mt-4 block text-center text-xs font-semibold text-forest hover:underline">
                        Lihat di jadwal saya
                    </a>

                    @unless ($webinar->sudahLewat())
                        <form action="{{ route('webinar.batal', $webinar->id_webinar) }}" method="POST" class="mt-2"
                              onsubmit="return confirm('Batalkan pendaftaran webinar ini?')">
                            @csrf @method('DELETE')
                            <button class="w-full text-center text-xs font-semibold text-muted hover:text-ember">Batalkan pendaftaran</button>
                        </form>
                    @endunless
                @elseif ($webinar->sudahLewat())
                    <p class="text-base font-extrabold">Webinar sudah selesai</p>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        Pendaftaran untuk sesi ini sudah ditutup. Cek webinar lain yang akan datang.
                    </p>
                    <a href="{{ route('webinar.index') }}"
                       class="mt-5 block rounded-full bg-forest px-5 py-3 text-center text-sm font-semibold text-white hover:bg-forestdim">
                        Lihat webinar lain
                    </a>
                @else
                    <p class="text-base font-extrabold">Daftar tanpa isi formulir</p>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        Data peserta diambil langsung dari profilmu:
                    </p>

                    <dl class="mt-4 space-y-2.5 rounded-2xl bg-canvas p-4 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">Nama</dt>
                            <dd class="font-semibold">{{ $profil->nama_lengkap ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">Email</dt>
                            <dd class="truncate font-semibold">{{ auth()->user()->email }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">Institusi</dt>
                            <dd class="truncate font-semibold">{{ $profil->institusi ?: '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">Jenjang</dt>
                            <dd class="font-semibold">{{ $profil->tingkat_pendidikan ?: '—' }}</dd>
                        </div>
                    </dl>

                    <form action="{{ route('webinar.ikut', $webinar->id_webinar) }}" method="POST">
                        @csrf
                        <button class="mt-5 w-full rounded-full bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forestdim">
                            Daftar webinar
                        </button>
                    </form>
                    <a href="{{ route('user.profil') }}" class="mt-3 block text-center text-xs font-semibold text-muted hover:text-ink">
                        Datanya belum sesuai? Perbarui profil
                    </a>
                @endif
            </div>
        </div>
    </section>

    @if ($sayaLeader)
        <section class="mb-4 rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Pengaturan leader</h2>
            <p class="mt-1 text-sm text-muted">Perbarui tautan meeting bila platform yang dipakai berubah.</p>

            <form action="{{ route('webinar.link', $webinar->id_webinar) }}" method="POST" class="mt-4 flex flex-wrap gap-2">
                @csrf @method('PUT')
                <input type="url" name="link_meeting" required value="{{ $webinar->link_meeting }}"
                       placeholder="https://meet.google.com/…"
                       class="min-w-[260px] flex-1 rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                <button class="rounded-full bg-forest px-6 py-3 text-sm font-semibold text-white hover:bg-forestdim">Simpan tautan</button>
            </form>
        </section>
    @endif

    <div class="grid gap-4 lg:grid-cols-[1.3fr_.7fr]">
        <section class="rounded-4xl bg-white p-7">
            <h2 class="text-sm font-bold">Tentang webinar ini</h2>
            <p class="mt-3 max-w-[64ch] whitespace-pre-line text-[15px] leading-relaxed">{{ $webinar->deskripsi }}</p>

            <div class="mt-7 flex items-center gap-4 border-t border-line pt-6">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-limesoft text-sm font-bold">
                    {{ strtoupper(mb_substr($webinar->pembicara ?: 'P', 0, 1)) }}
                </span>
                <div>
                    <p class="text-sm font-bold">{{ $webinar->pembicara ?: ($webinar->leader->profil->nama_lengkap ?? '-') }}</p>
                    <p class="text-xs text-muted">Pembicara &middot; Leader {{ $webinar->komunitas->nama_komunitas ?? '' }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-4xl bg-white p-7">
            <h2 class="text-sm font-bold">Peserta terdaftar</h2>

            <ul class="mt-4 space-y-3">
                @forelse ($peserta as $p)
                    <li class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-canvas text-xs font-bold">
                            {{ strtoupper(mb_substr($p->profil->nama_lengkap ?? 'P', 0, 1)) }}
                        </span>
                        <a href="{{ route('user.show', $p->id_user) }}" class="truncate text-sm hover:underline">
                            {{ $p->profil->nama_lengkap ?? 'Pengguna Eduvia' }}
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-muted">Belum ada peserta. Jadilah yang pertama.</li>
                @endforelse
            </ul>

            @if ($jumlahPeserta > $peserta->count())
                <p class="mt-4 text-xs text-muted">dan {{ $jumlahPeserta - $peserta->count() }} peserta lainnya</p>
            @endif
        </section>
    </div>
@endsection

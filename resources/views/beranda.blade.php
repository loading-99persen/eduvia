@extends('layouts.user')

@section('title', 'Beranda — Eduvia')

@php
    $warnaMinat = ['bg-tangerine', 'bg-lilac', 'bg-sun', 'bg-lime', 'bg-limesoft'];
@endphp

@section('content')

    {{-- Sapaan + minat belajar --}}
    <section class="mb-6 overflow-hidden rounded-4xl bg-white">
        <div class="grid gap-6 p-7 md:grid-cols-[1.1fr_.9fr] md:p-9">
            <div>
                <h1 class="headline text-[38px] font-extrabold md:text-[46px]">
                    Halo, {{ \Illuminate\Support\Str::of(auth()->user()->nama)->explode(' ')->first() }}.<br>
                    Mau belajar apa hari ini?
                </h1>
                <p class="mt-4 max-w-[52ch] text-[15px] leading-relaxed text-muted">
                    Beranda ini disusun dari minat belajarmu dan komunitas yang kamu ikuti.
                    Semakin sering kamu berdiskusi, semakin pas rekomendasinya.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($minat as $i => $m)
                        <span class="rounded-full {{ $warnaMinat[$i % count($warnaMinat)] }} px-4 py-2 text-sm font-semibold text-ink">
                            {{ $m->nama_minat }}
                        </span>
                    @endforeach
                    <a href="{{ route('user.onboarding') }}"
                       class="rounded-full border border-dashed border-muted px-4 py-2 text-sm font-semibold text-muted hover:border-ink hover:text-ink">
                        Ubah minat
                    </a>
                </div>
            </div>

            <div class="relative hidden rounded-3xl bg-forest p-7 text-white md:block">
                @include('partials.logo', ['teks' => false, 'ukuran' => 'h-12 w-auto'])
                <p class="mt-5 text-lg font-bold leading-snug">Belajar bareng lebih jauh daripada belajar sendirian.</p>
                <p class="mt-2 text-sm leading-relaxed text-white/75">
                    Gabung komunitas, ikut diskusi, dan hadiri webinar dari leader komunitas.
                </p>
                <a href="{{ route('komunitas.index') }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/35 px-4 py-2 text-sm font-semibold hover:bg-white hover:text-ink">
                    Jelajahi komunitas
                </a>
            </div>
        </div>
    </section>

    {{-- Tulis postingan --}}
    <section class="mb-6 rounded-4xl bg-white p-6">
        @if ($komunitasSaya->isEmpty())
            <div class="text-center">
                <p class="text-sm font-bold">Gabung komunitas dulu untuk mulai memposting</p>
                <p class="mx-auto mt-1.5 max-w-[46ch] text-sm leading-relaxed text-muted">
                    Setiap postingan selalu berada di dalam sebuah komunitas.
                </p>
                <a href="{{ route('komunitas.index') }}"
                   class="mt-4 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Cari komunitas</a>
            </div>
        @else
            <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kembali" value="{{ route('beranda') }}">
                <div class="flex items-start gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-limesoft text-sm font-bold">
                        {{ strtoupper(mb_substr(auth()->user()->nama, 0, 1)) }}
                    </span>
                    <div class="flex-1">
                        <label for="konten" class="sr-only">Isi postingan</label>
                        <textarea id="konten" name="konten" rows="2" required
                                  placeholder="Tanyakan sesuatu, atau bagikan yang baru kamu pelajari…"
                                  class="w-full resize-none border-0 bg-transparent p-0 text-[15px] leading-relaxed placeholder:text-muted focus:ring-0">{{ old('konten') }}</textarea>

                        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-line pt-4">
                            <label class="cursor-pointer rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                                Gambar
                                <input type="file" name="gambar" accept="image/*" class="hidden"
                                       onchange="this.previousSibling.textContent = this.files[0] ? ' ' + this.files[0].name : ' Gambar'">
                            </label>
                            <label class="cursor-pointer rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                                File
                                <input type="file" name="file" class="hidden"
                                       onchange="this.previousSibling.textContent = this.files[0] ? ' ' + this.files[0].name : ' File'">
                            </label>

                            <label class="sr-only" for="id_komunitas">Komunitas tujuan</label>
                            <select id="id_komunitas" name="id_komunitas" required
                                    class="rounded-full border-0 bg-canvas px-4 py-2 text-xs font-semibold focus:ring-2 focus:ring-forest">
                                <option value="">Pilih komunitas</option>
                                @foreach ($komunitasSaya as $k)
                                    <option value="{{ $k->id_komunitas }}" @selected(old('id_komunitas') == $k->id_komunitas)>
                                        {{ $k->nama_komunitas }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit"
                                    class="ml-auto rounded-full bg-forest px-6 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                                Posting
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </section>

    {{-- Feed --}}
    <div class="mb-4 flex items-center gap-2">
        <h2 class="text-sm font-bold">Direkomendasikan untukmu</h2>
        <span class="h-px flex-1 bg-line"></span>
    </div>

    <div class="space-y-4">
        @forelse ($posts as $post)
            @include('partials.post-card', ['post' => $post])
        @empty
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Belum ada yang bisa ditampilkan</p>
                <p class="mx-auto mt-2 max-w-[44ch] text-sm leading-relaxed text-muted">
                    Gabung minimal satu komunitas supaya beranda terisi diskusi yang sesuai minatmu.
                </p>
                <a href="{{ route('komunitas.index') }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                    Cari komunitas
                </a>
            </div>
        @endforelse
    </div>
@endsection

@section('aside')
    <div class="sticky top-28 space-y-4">

        {{-- Webinar terdekat --}}
        <div class="rounded-4xl bg-white p-5">
            <h2 class="mb-3 text-sm font-bold">Webinar terdekat</h2>

            <div class="space-y-3">
                @forelse ($webinarTerdekat as $w)
                    <a href="{{ route('webinar.show', $w->id_webinar) }}" class="block rounded-3xl bg-canvas p-4 hover:bg-limesoft">
                        <div class="flex items-start gap-3">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-center leading-none">
                                <span>
                                    <span class="block text-base font-extrabold">{{ $w->mulai->format('d') }}</span>
                                    <span class="block text-[10px] font-semibold text-muted">{{ $w->mulai->locale('id')->isoFormat('MMM') }}</span>
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold">{{ $w->judul }}</p>
                                <p class="mt-0.5 truncate text-xs text-muted">{{ $w->komunitas->nama_komunitas ?? '' }}</p>
                                <p class="mt-1 text-xs font-semibold text-forest" data-wib="{{ $w->mulai->format('Y-m-d H:i') }}">{{ $w->mulai->format('H:i') }} WIB</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-sm leading-relaxed text-muted">Belum ada webinar terjadwal.</p>
                @endforelse
            </div>

            <a href="{{ route('user.jadwal') }}" class="mt-4 inline-block text-xs font-semibold text-forest hover:underline">Lihat jadwal saya</a>
        </div>

        {{-- Komunitas yang mungkin cocok --}}
        <div class="rounded-4xl bg-white p-5">
            <h2 class="mb-3 text-sm font-bold">Mungkin cocok untukmu</h2>

            <div class="space-y-3">
                @forelse ($komunitasRekomendasi as $k)
                    <div class="flex items-center gap-3">
                        <a href="{{ route('komunitas.show', $k->id_komunitas) }}"
                           class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-canvas text-xs font-bold">
                            {{ strtoupper(mb_substr($k->nama_komunitas, 0, 2)) }}
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('komunitas.show', $k->id_komunitas) }}" class="block truncate text-sm font-semibold hover:underline">
                                {{ $k->nama_komunitas }}
                            </a>
                            <p class="text-xs text-muted">{{ $k->jumlah_member }} anggota</p>
                        </div>
                        <form action="{{ route('komunitas.gabung', $k->id_komunitas) }}" method="POST">
                            @csrf
                            <button class="rounded-full bg-forest px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-forestdim">Gabung</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm leading-relaxed text-muted">Kamu sudah bergabung ke semua komunitas yang tersedia.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

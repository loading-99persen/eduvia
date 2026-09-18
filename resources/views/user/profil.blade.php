@extends('layouts.user')

@section('title', 'Profil — Eduvia')

@php
    $warnaMinat = ['bg-tangerine', 'bg-lilac', 'bg-sun', 'bg-lime'];
    $lengkap = auth()->user()->profilLengkap();
@endphp

@section('content')

    @unless ($lengkap)
        <div class="mb-5 rounded-3xl border border-forest/30 bg-limesoft/50 p-6">
            <p class="text-sm font-bold">Lengkapi profilmu dulu</p>
            <p class="mt-1.5 max-w-[60ch] text-sm leading-relaxed text-muted">
                Nama lengkap dan jenjang pendidikan wajib diisi. Data ini dipakai otomatis saat kamu mendaftar webinar,
                jadi kamu tidak perlu mengisi formulir lagi nanti.
            </p>
        </div>
    @endunless

    {{-- Kartu identitas --}}
    <section class="mb-6 overflow-hidden rounded-4xl bg-white">
        <div class="h-28 bg-forest"></div>
        <div class="flex flex-wrap items-end gap-5 px-7 pb-7">
            <div class="-mt-12">
                @if ($profil->photo)
                    <img src="{{ asset('storage/' . $profil->photo) }}" alt=""
                         class="h-24 w-24 rounded-3xl border-4 border-white object-cover">
                @else
                    <span class="grid h-24 w-24 place-items-center rounded-3xl border-4 border-white bg-lime text-2xl font-extrabold">
                        {{ strtoupper(mb_substr($profil->nama_lengkap, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div class="min-w-0 flex-1 pt-2">
                <h1 class="text-2xl font-extrabold tracking-tight">{{ $profil->nama_lengkap }}</h1>
                <p class="mt-1 text-sm text-muted">
                    {{ $profil->tingkat_pendidikan ?: 'Jenjang belum diisi' }} &middot; {{ $profil->institusi ?: '—' }}
                </p>
            </div>
            <a href="#form-profil" class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                Ubah profil
            </a>
        </div>

        @if ($profil->bio)
            <p class="max-w-[62ch] px-7 pb-7 text-[15px] leading-relaxed text-muted">{{ $profil->bio }}</p>
        @endif

        <dl class="grid grid-cols-3 gap-px border-t border-line bg-line">
            <div class="bg-white px-7 py-5">
                <dt class="text-xs text-muted">Komunitas</dt>
                <dd class="mt-1 text-xl font-extrabold">{{ $statistik->komunitas }}</dd>
            </div>
            <div class="bg-white px-7 py-5">
                <dt class="text-xs text-muted">Postingan</dt>
                <dd class="mt-1 text-xl font-extrabold">{{ $statistik->postingan }}</dd>
            </div>
            <div class="bg-white px-7 py-5">
                <dt class="text-xs text-muted">Webinar</dt>
                <dd class="mt-1 text-xl font-extrabold">{{ $statistik->webinar }}</dd>
            </div>
        </dl>
    </section>

    {{-- Minat belajar --}}
    <section class="mb-6 rounded-4xl bg-white p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold">Minat belajar</h2>
                <p class="mt-1 text-sm text-muted">Dipakai untuk menyusun rekomendasi di berandamu.</p>
            </div>
            <a href="{{ route('user.onboarding') }}"
               class="rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">Ubah minat</a>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            @forelse ($minat as $i => $m)
                <span class="rounded-full {{ $warnaMinat[$i % count($warnaMinat)] }} px-4 py-2 text-sm font-semibold">
                    {{ $m->nama_minat }}
                </span>
            @empty
                <p class="text-sm text-muted">Belum ada minat yang dipilih.</p>
            @endforelse
        </div>
    </section>

    {{-- Form data diri --}}
    <section id="form-profil" class="rounded-4xl bg-white p-7">
        <h2 class="text-lg font-extrabold tracking-tight">Data diri</h2>
        <p class="mt-1 max-w-[58ch] text-sm leading-relaxed text-muted">
            Data ini dipakai otomatis saat kamu mendaftar webinar, jadi kamu tidak perlu mengisi formulir lagi.
        </p>

        <form action="{{ route('user.profil.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-5 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="sm:col-span-2">
                <label for="photo" class="mb-1.5 block text-sm font-semibold">Foto profil</label>
                <input id="photo" type="file" name="photo" accept="image/*"
                       class="block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-canvas file:px-5 file:py-2.5 file:text-sm file:font-semibold hover:file:bg-line">
                <p class="mt-1.5 text-xs text-muted">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
            </div>

            <div>
                <label for="nama_lengkap" class="mb-1.5 block text-sm font-semibold">Nama lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" required value="{{ old('nama_lengkap', $profil->nama_lengkap) }}"
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold">Email</label>
                <input id="email" value="{{ $user->email }}" disabled
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm text-muted">
            </div>

            <div>
                <label for="tingkat_pendidikan" class="mb-1.5 block text-sm font-semibold">Jenjang pendidikan</label>
                <select id="tingkat_pendidikan" name="tingkat_pendidikan" required
                        class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                    <option value="">Pilih jenjang</option>
                    @foreach (['SD', 'SMP', 'SMA/SMK', 'Mahasiswa', 'Lulusan', 'Umum'] as $jenjang)
                        <option value="{{ $jenjang }}" @selected(old('tingkat_pendidikan', $profil->tingkat_pendidikan) === $jenjang)>
                            {{ $jenjang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="institusi" class="mb-1.5 block text-sm font-semibold">Sekolah / universitas</label>
                <input id="institusi" name="institusi" value="{{ old('institusi', $profil->institusi) }}"
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            </div>

            <div>
                <label for="tanggal_lahir" class="mb-1.5 block text-sm font-semibold">Tanggal lahir</label>
                <input id="tanggal_lahir" type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', optional($profil->tanggal_lahir)->format('Y-m-d')) }}"
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            </div>

            <div>
                <label for="jenis_kelamin" class="mb-1.5 block text-sm font-semibold">Jenis kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin"
                        class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                    <option value="">Tidak diisi</option>
                    <option value="L" @selected(old('jenis_kelamin', $profil->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $profil->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="media_sosial" class="mb-1.5 block text-sm font-semibold">Media sosial (opsional)</label>
                <input id="media_sosial" name="media_sosial" value="{{ old('media_sosial', $profil->media_sosial) }}"
                       placeholder="Instagram, LinkedIn, atau tautan lain"
                       class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            </div>

            <div class="sm:col-span-2">
                <label for="bio" class="mb-1.5 block text-sm font-semibold">Bio</label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('bio', $profil->bio) }}</textarea>
                <p class="mt-1.5 text-xs text-muted">Ceritakan singkat apa yang sedang kamu pelajari.</p>
            </div>

            <div class="sm:col-span-2">
                <button class="rounded-full bg-forest px-6 py-3 text-sm font-semibold text-white hover:bg-forestdim">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </section>
@endsection

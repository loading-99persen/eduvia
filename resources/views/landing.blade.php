@extends('layouts.publik')

@section('title', 'Eduvia — Belajar bersama komunitasmu')

@section('content')

    <section class="mb-4 overflow-hidden rounded-4xl bg-white">
        <div class="grid gap-8 p-8 md:grid-cols-[1.1fr_.9fr] md:p-12">
            <div>
                <img src="{{ asset('images/logo-eduvia.png') }}" alt="Eduvia — Satu Platform, Seribu Inspirasi" class="mb-6 h-16 w-auto">
                <span class="inline-flex rounded-full bg-limesoft px-4 py-2 text-xs font-bold text-forest">Platform belajar bersama</span>
                <h1 class="headline mt-5 text-[42px] font-extrabold md:text-[60px]">
                    Belajar bersama,<br>bukan sendirian.
                </h1>
                <p class="mt-6 max-w-[54ch] text-[16px] leading-relaxed text-muted">
                    Temukan komunitas sesuai minatmu, berdiskusi lewat postingan, bertanya cepat di group chat,
                    dan ikuti webinar yang dibuat langsung oleh leader komunitas.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="rounded-full bg-forest px-7 py-3.5 text-sm font-semibold text-white hover:bg-forestdim">Mulai sekarang</a>
                    <a href="{{ route('login') }}" class="rounded-full bg-canvas px-7 py-3.5 text-sm font-semibold hover:bg-line">Sudah punya akun</a>
                </div>
            </div>

            <div class="rounded-3xl bg-forest p-8 text-white">
                <p class="text-lg font-bold leading-snug">Yang bisa kamu lakukan di sini</p>
                <ul class="mt-5 space-y-3 text-sm leading-relaxed text-white/80">
                    <li class="flex gap-3"><span class="text-lime">&#10003;</span> Gabung komunitas sesuai minat belajar</li>
                    <li class="flex gap-3"><span class="text-lime">&#10003;</span> Diskusi lewat postingan, komentar, dan balasan</li>
                    <li class="flex gap-3"><span class="text-lime">&#10003;</span> Group chat khusus anggota komunitas</li>
                    <li class="flex gap-3"><span class="text-lime">&#10003;</span> Daftar webinar tanpa isi formulir lagi</li>
                    <li class="flex gap-3"><span class="text-lime">&#10003;</span> Ajukan komunitas sendiri dan jadi leader</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['Komunitas', 'Ruang belajar dengan anggota, diskusi, dan group chat sendiri.', 'bg-lime'],
            ['Diskusi', 'Bertanya, berbagi rangkuman, dan saling mengoreksi.', 'bg-sun'],
            ['Webinar', 'Sesi langsung dari leader komunitas, jadwalnya tersimpan otomatis.', 'bg-lilac'],
        ] as [$judul, $isi, $warna])
            <article class="rounded-4xl bg-white p-7">
                <span class="block h-2.5 w-10 rounded-full {{ $warna }}"></span>
                <h2 class="mt-5 text-lg font-extrabold tracking-tight">{{ $judul }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-muted">{{ $isi }}</p>
            </article>
        @endforeach
    </section>
@endsection

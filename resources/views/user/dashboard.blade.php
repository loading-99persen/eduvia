@extends('layouts.user')

@section('title', 'Dashboard — Eduvia')

@php
    $puncak = max($aktivitasMingguan) ?: 1;

    $kartu = [
        ['label' => 'Komunitas diikuti', 'nilai' => $statistik->komunitas, 'warna' => 'bg-lime'],
        ['label' => 'Webinar diikuti',   'nilai' => $statistik->webinar,   'warna' => 'bg-sun'],
        ['label' => 'Postingan',         'nilai' => $statistik->postingan, 'warna' => 'bg-tangerine'],
        ['label' => 'Interaksi diterima','nilai' => $statistik->interaksi, 'warna' => 'bg-lilac'],
    ];
@endphp

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Aktivitas belajarmu</h1>
        <p class="mt-3 max-w-[54ch] text-[15px] leading-relaxed text-muted">
            Ringkasan singkat dari apa yang sudah kamu ikuti dan kerjakan di Eduvia.
        </p>
    </header>

    <div class="mb-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kartu as $k)
            <div class="rounded-4xl bg-white p-6">
                <span class="block h-2.5 w-10 rounded-full {{ $k['warna'] }}"></span>
                <p class="mt-5 text-4xl font-extrabold tracking-tight">{{ $k['nilai'] }}</p>
                <p class="mt-1 text-sm text-muted">{{ $k['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.1fr_.9fr]">

        <section class="rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Aktivitas 7 hari terakhir</h2>
            <p class="mt-1 text-sm text-muted">Jumlah postingan, komentar, dan like yang kamu buat.</p>

            <div class="mt-8 flex h-44 items-end gap-3">
                @foreach ($aktivitasMingguan as $i => $nilai)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <span class="text-xs font-semibold text-muted">{{ $nilai }}</span>
                        <div class="w-full rounded-t-2xl {{ $nilai > 0 && $nilai === $puncak ? 'bg-forest' : 'bg-limesoft' }}"
                             style="height: {{ max(6, round(($nilai / $puncak) * 100)) }}%"></div>
                        <span class="text-xs text-muted">{{ $labelHari[$i] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Terbaru</h2>

            @if ($aktivitas->isEmpty())
                <p class="mt-4 text-sm leading-relaxed text-muted">
                    Belum ada aktivitas. Mulailah dengan bergabung ke komunitas dan menulis satu pertanyaan.
                </p>
            @else
                <ul class="mt-4 divide-y divide-line">
                    @foreach ($aktivitas as $a)
                        <li class="flex gap-3 py-3.5">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-tangerine"></span>
                            <div class="min-w-0">
                                @if ($a->tautan)
                                    <a href="{{ $a->tautan }}" class="text-sm leading-relaxed hover:underline">{{ $a->teks }}</a>
                                @else
                                    <p class="text-sm leading-relaxed">{{ $a->teks }}</p>
                                @endif
                                <p class="mt-0.5 text-xs text-muted">{{ \Carbon\Carbon::parse($a->waktu)->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection

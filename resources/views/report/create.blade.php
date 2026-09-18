@extends('layouts.user')

@section('title', 'Laporkan konten — Eduvia')

@php
    $labelTarget = [
        'post'      => 'postingan',
        'komentar'  => 'komentar',
        'user'      => 'pengguna',
        'komunitas' => 'komunitas',
        'webinar'   => 'webinar',
    ];

    $contoh = [
        'Berisi ujaran kebencian atau menghina orang lain',
        'Spam atau promosi yang tidak berkaitan',
        'Informasi menyesatkan',
        'Konten tidak pantas',
    ];
@endphp

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Laporkan konten</h1>
        <p class="mt-3 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Kamu melaporkan {{ $labelTarget[$tipe] ?? $tipe }} #{{ $idTarget }}.
            Jelaskan apa yang bermasalah supaya admin bisa meninjau dengan tepat.
        </p>
    </header>

    <form action="{{ route('report.store') }}" method="POST" class="rounded-4xl bg-white p-7">
        @csrf
        <input type="hidden" name="tipe_target" value="{{ $tipe }}">
        <input type="hidden" name="id_target" value="{{ $idTarget }}">

        <label for="alasan" class="mb-1.5 block text-sm font-semibold">Alasan pelaporan</label>
        <textarea id="alasan" name="alasan" rows="5" required minlength="10"
                  placeholder="Ceritakan apa yang kamu temukan…"
                  class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('alasan') }}</textarea>

        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($contoh as $teks)
                <button type="button"
                        onclick="document.getElementById('alasan').value = @js($teks); document.getElementById('alasan').focus();"
                        class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold text-muted hover:text-ink">
                    {{ $teks }}
                </button>
            @endforeach
        </div>

        <div class="mt-6 rounded-2xl bg-canvas p-5 text-sm leading-relaxed text-muted">
            Laporan dikirim ke admin dan diproses secara terpisah. Identitasmu tidak dibagikan
            kepada pihak yang dilaporkan.
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-line pt-6">
            <button class="rounded-full bg-forest px-7 py-3 text-sm font-semibold text-white hover:bg-forestdim">Kirim laporan</button>
            <a href="{{ $kembali ?: route('beranda') }}" class="text-sm font-semibold text-muted hover:text-ink">Batal</a>
        </div>
    </form>
@endsection

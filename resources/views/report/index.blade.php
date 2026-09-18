@extends('layouts.user')

@section('title', 'Laporan saya — Eduvia')

@php
    $gaya = [
        'proses'   => ['Sedang ditinjau', 'bg-sun text-ink'],
        'diterima' => ['Ditindaklanjuti', 'bg-forest text-lime'],
        'ditolak'  => ['Tidak dilanjutkan', 'bg-canvas text-muted'],
    ];

    $labelTarget = [
        'post'      => 'Postingan',
        'komentar'  => 'Komentar',
        'user'      => 'Pengguna',
        'komunitas' => 'Komunitas',
        'webinar'   => 'Webinar',
    ];
@endphp

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Laporan saya</h1>
        <p class="mt-3 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Konten yang kamu laporkan beserta hasil peninjauannya. Identitas pelapor tidak ditampilkan
            kepada pihak yang dilaporkan.
        </p>
    </header>

    <div class="space-y-4">
        @forelse ($reports as $r)
            @php [$labelStatus, $warnaStatus] = $gaya[$r->status] ?? $gaya['proses']; @endphp

            <article class="rounded-4xl bg-white p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full {{ $warnaStatus }} px-3 py-1 text-xs font-bold">{{ $labelStatus }}</span>
                    <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">
                        {{ $labelTarget[$r->tipe_target] ?? $r->tipe_target }} #{{ $r->id_target }}
                    </span>
                    <span class="ml-auto text-xs text-muted">
                        {{ optional($r->dibuat_pada)->locale('id')->isoFormat('D MMMM Y, HH:mm') }}
                    </span>
                </div>

                <p class="mt-4 max-w-[64ch] text-[15px] leading-relaxed">{{ $r->alasan }}</p>

                @if ($r->tanggapan_admin)
                    <div class="mt-4 rounded-2xl bg-canvas p-4">
                        <p class="text-xs font-bold">Tanggapan admin</p>
                        <p class="mt-1 text-sm leading-relaxed text-muted">{{ $r->tanggapan_admin }}</p>
                    </div>
                @endif

                @if ($r->tipe_target === 'post')
                    <a href="{{ route('post.show', $r->id_target) }}"
                       class="mt-4 inline-flex rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">
                        Lihat postingan
                    </a>
                @endif
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Belum ada laporan</p>
                <p class="mx-auto mt-2 max-w-[48ch] text-sm leading-relaxed text-muted">
                    Kalau menemukan konten yang melanggar, gunakan tombol &ldquo;Laporkan&rdquo; pada postingan
                    atau komentar tersebut.
                </p>
            </div>
        @endforelse
    </div>
@endsection

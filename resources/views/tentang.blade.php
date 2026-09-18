@extends(auth()->check() ? 'layouts.user' : 'layouts.publik')

@section('title', 'Tentang — Eduvia')

@section('content')

    <section class="mb-4 overflow-hidden rounded-4xl bg-white p-7 md:p-10">
        <h1 class="headline max-w-[15ch] text-[42px] font-extrabold md:text-[58px]">
            Belajar bersama, bukan sendirian.
        </h1>
        <p class="mt-6 max-w-[58ch] text-[16px] leading-relaxed text-muted">
            Eduvia mempertemukan pelajar, mahasiswa, dan siapa pun yang sedang belajar dalam satu tempat:
            komunitas untuk berdiskusi, group chat untuk bertanya cepat, dan webinar yang dibuat langsung oleh
            leader komunitas.
        </p>

        <div class="mt-8 flex flex-wrap gap-2">
            <span class="rounded-full bg-tangerine px-5 py-2.5 text-sm font-semibold">Komunitas</span>
            <span class="rounded-full bg-lilac px-5 py-2.5 text-sm font-semibold">Diskusi</span>
            <span class="rounded-full bg-sun px-5 py-2.5 text-sm font-semibold">Group chat</span>
            <span class="rounded-full bg-lime px-5 py-2.5 text-sm font-semibold">Webinar</span>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-3">
        <section class="rounded-4xl bg-forest p-7 text-white md:col-span-2">
            <h2 class="text-lg font-extrabold tracking-tight">Cara kerjanya</h2>
            <ol class="mt-5 space-y-5">
                @foreach ([
                    ['Pilih minat belajarmu', 'Beranda dan rekomendasi komunitas disusun dari topik yang kamu pilih.'],
                    ['Gabung komunitas', 'Setiap komunitas punya ruang diskusi, daftar anggota, dan group chat sendiri.'],
                    ['Ikuti webinar', 'Cukup satu klik. Data pesertamu diambil dari profil, tanpa isi formulir lagi.'],
                    ['Buat komunitasmu sendiri', 'Ajukan ke admin. Setelah disetujui, kamu jadi leader komunitas itu.'],
                ] as $i => $langkah)
                    <li class="flex gap-4">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-lime text-sm font-extrabold text-ink">{{ $i + 1 }}</span>
                        <div>
                            <p class="text-sm font-bold">{{ $langkah[0] }}</p>
                            <p class="mt-1 max-w-[52ch] text-sm leading-relaxed text-white/75">{{ $langkah[1] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="rounded-4xl bg-white p-7">
            <h2 class="text-lg font-extrabold tracking-tight">Aturan singkat</h2>
            <ul class="mt-5 space-y-4 text-sm leading-relaxed text-muted">
                <li>Diskusi dijaga tetap sopan dan berkaitan dengan belajar.</li>
                <li>Tautan meeting hanya untuk peserta terdaftar.</li>
                <li>Konten yang melanggar bisa dilaporkan ke admin lewat tombol laporan.</li>
            </ul>
            <a href="{{ route('report.index') }}"
               class="mt-6 inline-flex rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">
                Laporan saya
            </a>
        </section>
    </div>
@endsection

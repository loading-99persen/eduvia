@extends('layouts.user')

@section('title', 'Pengajuan saya — Eduvia')

@php
    $gaya = [
        'proses'    => ['Menunggu ditinjau', 'bg-sun text-ink'],
        'disetujui' => ['Disetujui',         'bg-forest text-lime'],
        'ditolak'   => ['Ditolak',           'bg-tangerine text-ink'],
    ];
@endphp

@section('content')

    <header class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Pengajuan saya</h1>
            <p class="mt-3 max-w-[54ch] text-[15px] leading-relaxed text-muted">
                Ajukan komunitas baru atau webinar untuk komunitas yang kamu pimpin. Admin akan meninjau
                sebelum keduanya tayang.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pengajuan.create', ['tipe' => 'komunitas']) }}"
               class="rounded-full bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forestdim">Ajukan komunitas</a>
            <a href="{{ route('pengajuan.create', ['tipe' => 'webinar']) }}"
               class="rounded-full bg-canvas px-5 py-3 text-sm font-semibold hover:bg-line">Ajukan webinar</a>
        </div>
    </header>

    <div class="space-y-4">
        @forelse ($pengajuan as $p)
            @php [$labelStatus, $warnaStatus] = $gaya[$p->status] ?? $gaya['proses']; @endphp

            <article class="rounded-4xl bg-white p-6">
                <div class="flex flex-wrap items-start gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-canvas text-xs font-bold uppercase">
                        {{ $p->tipe === 'webinar' ? 'WBN' : 'KOM' }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full {{ $warnaStatus }} px-3 py-1 text-xs font-bold">{{ $labelStatus }}</span>
                            <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">
                                {{ $p->tipe === 'webinar' ? 'Webinar' : 'Komunitas baru' }}
                            </span>
                            @if ($p->kategori)
                                <span class="text-xs text-muted">{{ $p->kategori }}</span>
                            @endif
                        </div>

                        <h2 class="mt-2 text-lg font-extrabold tracking-tight">{{ $p->judul }}</h2>

                        <p class="mt-2 max-w-[62ch] text-sm leading-relaxed text-muted">
                            {{ \Illuminate\Support\Str::limit($p->deskripsi, 200) }}
                        </p>

                        @if ($p->tipe === 'webinar')
                            <p class="mt-2 text-sm text-muted">
                                @if ($p->komunitas)
                                    Untuk komunitas {{ $p->komunitas->nama_komunitas }} &middot;
                                @endif
                                @if ($p->tanggal)
                                    {{ $p->tanggal->locale('id')->isoFormat('D MMMM Y') }}
                                    @if ($p->waktu) &middot; {{ substr($p->waktu, 0, 5) }} WIB @endif
                                @endif
                                @if ($p->pembicara) &middot; Pembicara {{ $p->pembicara }} @endif
                            </p>
                        @endif

                        <p class="mt-2 text-xs text-muted">
                            Diajukan {{ optional($p->tanggal_pengajuan)->locale('id')->isoFormat('D MMMM Y, HH:mm') }}
                            @if ($p->diproses_pada)
                                &middot; diproses {{ $p->diproses_pada->locale('id')->isoFormat('D MMMM Y') }}
                            @endif
                        </p>

                        @if ($p->status === 'ditolak' && $p->catatan_admin)
                            <div class="mt-4 rounded-2xl border border-tangerine bg-tangerine/10 p-4">
                                <p class="text-xs font-bold">Catatan admin</p>
                                <p class="mt-1 text-sm leading-relaxed">{{ $p->catatan_admin }}</p>
                            </div>
                        @endif

                        @if ($p->status === 'disetujui' && $p->tipe === 'komunitas' && $p->id_komunitas)
                            <a href="{{ route('komunitas.show', $p->id_komunitas) }}"
                               class="mt-4 inline-flex rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">
                                Buka komunitas
                            </a>
                        @endif
                    </div>

                    @if ($p->status === 'proses')
                        <form action="{{ route('pengajuan.destroy', $p->id_pengajuan) }}" method="POST"
                              onsubmit="return confirm('Batalkan pengajuan ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-full px-4 py-2 text-sm font-semibold text-muted hover:text-ember">Batalkan</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Belum ada pengajuan</p>
                <p class="mx-auto mt-2 max-w-[48ch] text-sm leading-relaxed text-muted">
                    Kalau topik yang kamu cari belum ada komunitasnya, ajukan saja. Kalau disetujui,
                    kamu langsung menjadi leader komunitas tersebut.
                </p>
                <a href="{{ route('pengajuan.create', ['tipe' => 'komunitas']) }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                    Buat pengajuan pertama
                </a>
            </div>
        @endforelse
    </div>
@endsection

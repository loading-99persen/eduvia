@extends('layouts.admin')

@section('title', 'Dashboard admin — Eduvia')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Ringkasan platform</h1>
        <p class="mt-3 text-[15px] text-muted">Pantau pengajuan yang menunggu dan aktivitas terbaru.</p>
    </header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Pengguna', $statistik->user, 'bg-lime'],
            ['Komunitas', $statistik->komunitas, 'bg-sun'],
            ['Webinar', $statistik->webinar, 'bg-lilac'],
            ['Postingan', $statistik->postingan, 'bg-tangerine'],
        ] as [$label, $nilai, $warna])
            <div class="rounded-4xl bg-white p-6">
                <span class="block h-2.5 w-10 rounded-full {{ $warna }}"></span>
                <p class="mt-5 text-4xl font-extrabold tracking-tight">{{ $nilai }}</p>
                <p class="mt-1 text-sm text-muted">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.pengajuan', ['tipe' => 'komunitas']) }}" class="rounded-4xl bg-forest p-6 text-white hover:bg-forestdim">
            <p class="text-3xl font-extrabold">{{ $statistik->pengajuanKomunitas }}</p>
            <p class="mt-1 text-sm text-white/75">Pengajuan komunitas menunggu</p>
        </a>
        <a href="{{ route('admin.pengajuan', ['tipe' => 'webinar']) }}" class="rounded-4xl bg-forest p-6 text-white hover:bg-forestdim">
            <p class="text-3xl font-extrabold">{{ $statistik->pengajuanWebinar }}</p>
            <p class="mt-1 text-sm text-white/75">Pengajuan webinar menunggu</p>
        </a>
        <a href="{{ route('admin.laporan') }}" class="rounded-4xl bg-tangerine p-6 hover:bg-forestdim hover:text-white">
            <p class="text-3xl font-extrabold">{{ $statistik->laporan }}</p>
            <p class="mt-1 text-sm">Laporan belum ditinjau</p>
        </a>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <section class="rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Pengajuan terbaru</h2>
            <ul class="mt-4 divide-y divide-line">
                @forelse ($pengajuanTerbaru as $p)
                    <li class="py-3">
                        <p class="text-sm font-semibold">{{ $p->judul }}</p>
                        <p class="mt-0.5 text-xs text-muted">
                            {{ $p->tipe }} &middot; {{ $p->user->profil->nama_lengkap ?? '-' }} &middot; {{ $p->label_status }}
                        </p>
                    </li>
                @empty
                    <li class="py-3 text-sm text-muted">Belum ada pengajuan.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Laporan terbaru</h2>
            <ul class="mt-4 divide-y divide-line">
                @forelse ($laporanTerbaru as $r)
                    <li class="py-3">
                        <p class="text-sm font-semibold">{{ $r->tipe_target }} #{{ $r->id_target }}</p>
                        <p class="mt-0.5 line-clamp-2 text-xs text-muted">{{ $r->alasan }}</p>
                    </li>
                @empty
                    <li class="py-3 text-sm text-muted">Belum ada laporan.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-4xl bg-white p-6">
            <h2 class="text-sm font-bold">Pengguna terbaru</h2>
            <ul class="mt-4 divide-y divide-line">
                @forelse ($userTerbaru as $u)
                    <li class="py-3">
                        <p class="text-sm font-semibold">{{ $u->profil->nama_lengkap ?? 'Tanpa nama' }}</p>
                        <p class="mt-0.5 text-xs text-muted">{{ $u->email }}</p>
                    </li>
                @empty
                    <li class="py-3 text-sm text-muted">Belum ada pengguna.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection

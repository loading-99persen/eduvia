<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Eduvia')</title>
    @include('partials.tema')
</head>
<body class="bg-canvas font-sans text-ink">

@php
    $menuAdmin = [
        ['Dashboard', 'admin.dashboard'],
        ['Pengajuan', 'admin.pengajuan'],
        ['Pengguna',  'admin.users'],
        ['Komunitas', 'admin.komunitas'],
        ['Webinar',   'admin.webinar'],
        ['Laporan',   'admin.laporan'],
        ['Moderasi',  'admin.moderasi'],
    ];
@endphp

<header class="sticky top-0 z-40 bg-canvas/85 backdrop-blur">
    <div class="mx-auto max-w-[1400px] px-4 py-4 lg:px-8">
        <div class="flex flex-wrap items-center gap-3 rounded-full bg-white px-4 py-3 sm:px-6">
            <span class="flex items-center gap-2.5">
                @include('partials.logo', ['teks' => false, 'ukuran' => 'h-10 w-auto'])
                <span class="rounded-full bg-forest px-3 py-1 text-xs font-bold text-lime">Admin</span>
            </span>

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('beranda') }}" class="rounded-full bg-canvas px-4 py-2 text-sm font-semibold hover:bg-line">Lihat sisi user</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="rounded-full bg-forest px-5 py-2 text-sm font-semibold text-white hover:bg-forestdim">Keluar</button>
                </form>
            </div>
        </div>

        <nav class="mt-3 flex gap-1 overflow-x-auto rounded-full bg-white p-1">
            @foreach ($menuAdmin as [$label, $rute])
                <a href="{{ route($rute) }}"
                   class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition
                          {{ request()->routeIs($rute) ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

<main class="mx-auto max-w-[1400px] px-4 pb-16 pt-6 lg:px-8">
    @if (session('success'))
        <div class="mb-5 rounded-2xl bg-forest px-5 py-4 text-sm text-white">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-2xl border border-tangerine bg-tangerine/10 px-5 py-4 text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <ul class="mb-5 space-y-1 rounded-2xl border border-tangerine bg-tangerine/10 px-5 py-4 text-sm">
            @foreach ($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach
        </ul>
    @endif

    @yield('content')
</main>

@include('partials.footer')

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Eduvia')</title>
    @include('partials.tema')
    @stack('styles')
</head>
<body class="bg-canvas font-sans text-ink">

    @include('partials.header')

    <div class="mx-auto flex max-w-[1400px] gap-6 px-4 pb-16 lg:px-8">

        @include('partials.sidebar')

        <main class="min-w-0 flex-1 py-6">

            {{-- Pesan sistem --}}
            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl bg-forest px-5 py-4 text-sm text-white">
                    <span class="mt-0.5 text-lime">&#10003;</span>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-tangerine bg-tangerine/10 px-5 py-4 text-sm text-ink">
                    <span class="mt-0.5">&#9888;</span>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-tangerine bg-tangerine/10 px-5 py-4 text-sm text-ink">
                    <p class="font-bold">Periksa kembali isian berikut:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Kolom kanan (opsional per halaman) --}}
        @hasSection('aside')
            <aside class="hidden w-[320px] shrink-0 py-6 xl:block">
                @yield('aside')
            </aside>
        @endif
    </div>

    @include('partials.footer')

    {{-- Navigasi bawah khusus layar kecil --}}
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-white/95 backdrop-blur lg:hidden">
        <div class="mx-auto flex max-w-lg items-center justify-between px-4 py-2">
            @foreach ([
                ['Beranda', 'beranda'],
                ['Jelajahi', 'komunitas.index'],
                ['Jadwal', 'user.jadwal'],
                ['Pengajuan', 'pengajuan.index'],
                ['Profil', 'user.profil'],
            ] as [$label, $rute])
                <a href="{{ route($rute) }}"
                   class="rounded-2xl px-3 py-2 text-xs font-semibold {{ request()->routeIs($rute) ? 'bg-forest text-white' : 'text-muted' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>
    <div class="h-16 lg:hidden"></div>

    @stack('scripts')
</body>
</html>

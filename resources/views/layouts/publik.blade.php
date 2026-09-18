<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Eduvia')</title>
    @include('partials.tema')
</head>
<body class="bg-canvas font-sans text-ink">

    <header class="sticky top-0 z-40 bg-canvas/85 backdrop-blur">
        <div class="mx-auto max-w-[1200px] px-4 py-4 lg:px-8">
            <div class="flex items-center gap-4 rounded-full bg-white px-4 py-3 sm:px-6">
                <a href="{{ route('landing') }}" class="flex shrink-0 items-center gap-2.5">
                    @include('partials.logo', ['teks' => false, 'ukuran' => 'h-10 w-auto'])
                </a>

                <nav class="mx-auto hidden items-center gap-1 rounded-full bg-canvas p-1 md:flex">
                    @foreach ([['Beranda', 'landing'], ['Tentang', 'tentang'], ['Kontak', 'kontak']] as [$label, $rute])
                        <a href="{{ route($rute) }}"
                           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs($rute) ? 'bg-white text-ink shadow-sm' : 'text-muted hover:text-ink' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <div class="ml-auto flex items-center gap-2 md:ml-0">
                    @auth
                        <a href="{{ route('beranda') }}" class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Masuk aplikasi</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2.5 text-sm font-semibold text-muted hover:text-ink">Login</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Daftar</a>
                    @endauth
                </div>
            </div>

            <nav class="mt-3 flex gap-1 overflow-x-auto rounded-full bg-white p-1 md:hidden">
                @foreach ([['Beranda', 'landing'], ['Tentang', 'tentang'], ['Kontak', 'kontak']] as [$label, $rute])
                    <a href="{{ route($rute) }}"
                       class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold {{ request()->routeIs($rute) ? 'bg-forest text-white' : 'text-muted' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-[1200px] px-4 pb-16 lg:px-8">
        @if (session('success'))
            <div class="mb-5 rounded-2xl bg-forest px-5 py-4 text-sm text-white">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-tangerine bg-tangerine/10 px-5 py-4 text-sm">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    @auth
        @include('partials.footer')
    @else
        <footer class="mx-auto max-w-[1200px] px-4 pb-10 text-sm text-muted lg:px-8">
            <div class="flex flex-wrap items-center gap-3 border-t border-line pt-6">
                <span>&copy; {{ date('Y') }} Eduvia</span>
                <a href="{{ route('tentang') }}" class="ml-auto hover:text-ink">Tentang</a>
                <a href="{{ route('kontak') }}" class="hover:text-ink">Kontak</a>
            </div>
        </footer>
    @endauth
</body>
</html>

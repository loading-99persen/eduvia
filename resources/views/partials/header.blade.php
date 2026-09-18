@php
    $me      = auth()->user();
    $nama    = $me?->profil?->nama_lengkap ?? 'Pengguna Eduvia';
    $foto    = $me?->profil?->photo ? asset('storage/' . $me->profil->photo) : null;
    $inisial = strtoupper(mb_substr($nama, 0, 1));

    $navUtama = [
        ['label' => 'Beranda',  'route' => 'beranda'],
        ['label' => 'Jelajahi', 'route' => 'komunitas.index'],
        ['label' => 'Webinar',  'route' => 'webinar.index'],
        ['label' => 'Tentang',  'route' => 'tentang'],
    ];

    $belumDibaca = $belumDibaca ?? 0;
@endphp

<header class="sticky top-0 z-40 bg-canvas/85 backdrop-blur">
    <div class="mx-auto max-w-[1400px] px-4 py-4 lg:px-8">
        <div class="flex items-center gap-4 rounded-full bg-white px-4 py-3 shadow-[0_1px_0_rgba(16,19,18,.06)] sm:px-6">

            <a href="{{ route('beranda') }}" class="flex shrink-0 items-center gap-2.5">
                @include('partials.logo', ['teks' => false, 'ukuran' => 'h-10 w-auto'])
            </a>

            <nav class="mx-auto hidden items-center gap-1 rounded-full bg-canvas p-1 md:flex">
                @foreach ($navUtama as $item)
                    @php $aktif = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="rounded-full px-4 py-2 text-sm font-semibold transition
                              {{ $aktif ? 'bg-white text-ink shadow-sm' : 'text-muted hover:text-ink' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-2 md:ml-0">

                <form action="{{ route('komunitas.index') }}" method="GET" class="hidden lg:block">
                    <label class="sr-only" for="cari-header">Cari komunitas</label>
                    <div class="flex items-center gap-2 rounded-full bg-canvas px-4 py-2">
                        <svg width="15" height="15" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <circle cx="9" cy="9" r="6" stroke="#6E6880" stroke-width="2"/>
                            <path d="M13.5 13.5 18 18" stroke="#6E6880" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input id="cari-header" type="text" name="cari" value="{{ request('cari') }}"
                               placeholder="Cari komunitas atau topik"
                               class="w-52 bg-transparent text-sm placeholder:text-muted focus:outline-none">
                    </div>
                </form>

                <a href="{{ route('notifikasi.index') }}" aria-label="Notifikasi"
                   class="relative grid h-10 w-10 place-items-center rounded-full bg-canvas hover:bg-line">
                    <svg width="17" height="17" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5 8a5 5 0 0 1 10 0c0 4 1.5 5 1.5 5h-13S5 12 5 8Z" stroke="#252128" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M8.3 16a2 2 0 0 0 3.4 0" stroke="#252128" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                    @if ($belumDibaca > 0)
                        <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-[20px] place-items-center rounded-full bg-tangerine px-1 text-[10px] font-bold text-ink ring-2 ring-white">
                            {{ $belumDibaca > 9 ? '9+' : $belumDibaca }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('user.profil') }}"
                   class="flex items-center gap-2 rounded-full bg-forest py-1.5 pl-1.5 pr-4 text-sm font-semibold text-white hover:bg-forestdim">
                    @if ($foto)
                        <img src="{{ $foto }}" alt="" class="h-7 w-7 rounded-full object-cover">
                    @else
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-lime text-xs font-bold text-ink">{{ $inisial }}</span>
                    @endif
                    <span class="hidden sm:inline">{{ \Illuminate\Support\Str::of($nama)->explode(' ')->first() }}</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                    @csrf
                    <button type="submit" title="Keluar"
                            class="grid h-10 w-10 place-items-center rounded-full bg-canvas text-muted hover:bg-line hover:text-ink">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4"/><path d="m16 16 4-4-4-4"/><path d="M20 12H10"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <nav class="mt-3 flex gap-1 overflow-x-auto rounded-full bg-white p-1 md:hidden">
            @foreach ($navUtama as $item)
                @php $aktif = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold {{ $aktif ? 'bg-forest text-white' : 'text-muted' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

@php
    $menuAdmin = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'ikon' => '📊'],
        ['label' => 'Users',     'route' => 'admin.users',     'ikon' => '👥'],
        ['label' => 'Komunitas', 'route' => 'admin.komunitas', 'ikon' => '🎓'],
        ['label' => 'Pengajuan', 'route' => 'admin.pengajuan', 'ikon' => '📩'],
        ['label' => 'Webinar',   'route' => 'admin.webinar',   'ikon' => '📚'],
        ['label' => 'Report',    'route' => 'admin.laporan',   'ikon' => '🚩'],
        ['label' => 'Moderasi',  'route' => 'admin.moderasi',  'ikon' => '🧹'],
    ];
@endphp

<aside class="sticky top-0 hidden h-screen w-72 shrink-0 flex-col overflow-y-auto rounded-r-[35px] bg-forest px-6 pb-6 pt-8 text-white shadow-xl lg:flex">

    {{-- BRAND --}}

    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold tracking-tight">Eduvia Admin</h1>
        <p class="mt-2 text-sm text-white/70">Education Management</p>
    </div>

    {{-- MENU --}}

    <nav class="space-y-3">
        @foreach ($menuAdmin as $item)
            @php $aktif = request()->routeIs($item['route'] . '*'); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-4 rounded-2xl px-5 py-3 text-sm font-semibold transition duration-300
                      {{ $aktif ? 'bg-white/15' : 'hover:bg-white/10' }}">
                <span class="text-lg">{{ $item['ikon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- AKSI --}}

    <div class="mt-auto space-y-3 pt-8">
        <a href="{{ route('beranda') }}"
           class="block rounded-2xl bg-white/10 py-3 text-center text-sm font-semibold transition duration-300 hover:bg-white/20">
            Lihat sisi user
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full rounded-2xl bg-tangerine py-3 text-sm font-semibold text-ink shadow-sm transition duration-300 hover:brightness-95">
                Logout
            </button>
        </form>
    </div>

</aside>

{{-- MENU VERSI MOBILE --}}

<nav class="fixed inset-x-0 bottom-0 z-40 flex gap-1 overflow-x-auto border-t border-line bg-white px-3 py-2 lg:hidden">
    @foreach ($menuAdmin as $item)
        @php $aktif = request()->routeIs($item['route'] . '*'); @endphp
        <a href="{{ route($item['route']) }}"
           class="whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold {{ $aktif ? 'bg-forest text-white' : 'text-muted' }}">
            {{ $item['ikon'] }} {{ $item['label'] }}
        </a>
    @endforeach
</nav>

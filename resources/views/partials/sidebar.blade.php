@php
    $me = auth()->user();

    $menu = [
        [
            'label' => 'Dashboard',
            'route' => 'user.dashboard',
            'icon'  => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="13" y="3" width="7" height="4" rx="2"/><rect x="13" y="10" width="7" height="10" rx="2"/><rect x="3" y="13" width="7" height="7" rx="2"/>',
        ],
        [
            'label' => 'Komunitas saya',
            'route' => 'user.komunitas',
            'icon'  => '<circle cx="8" cy="8" r="3"/><circle cx="16" cy="9" r="2.4"/><path d="M3 19c0-2.8 2.2-5 5-5s5 2.2 5 5"/><path d="M15 14.2c2.3.3 4 2.2 4 4.8"/>',
        ],
        [
            'label' => 'Jadwal',
            'route' => 'user.jadwal',
            'icon'  => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/>',
        ],
        [
            'label' => 'Pengajuan',
            'route' => 'pengajuan.index',
            'icon'  => '<path d="M6 3h8l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
        ],
        [
            'label' => 'Notifikasi',
            'route' => 'notifikasi.index',
            'icon'  => '<path d="M6 9a6 6 0 0 1 12 0c0 4.5 1.6 5.5 1.6 5.5H4.4S6 13.5 6 9Z"/><path d="M10 18a2 2 0 0 0 4 0"/>',
        ],
        [
            'label' => 'Laporan saya',
            'route' => 'report.index',
            'icon'  => '<path d="M4 4h16v12H7l-3 3z"/><path d="M12 8v3M12 13.5v.5"/>',
        ],
    ];

    $komunitasSaya = $komunitasSidebar ?? collect();
    $belumDibaca   = $belumDibaca ?? 0;
@endphp

<aside class="hidden w-[260px] shrink-0 py-6 lg:block">
    <div class="sticky top-28 space-y-4">

        <nav class="rounded-4xl bg-white p-3">
            <ul class="space-y-1">
                @foreach ($menu as $item)
                    @php $aktif = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                                  {{ $aktif ? 'bg-forest text-white' : 'text-muted hover:bg-canvas hover:text-ink' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="{{ $aktif ? '#62CFB6' : 'currentColor' }}" stroke-width="1.7"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                {!! $item['icon'] !!}
                            </svg>
                            {{ $item['label'] }}

                            @if ($item['route'] === 'notifikasi.index' && $belumDibaca > 0)
                                <span class="ml-auto rounded-full bg-tangerine px-2 py-0.5 text-[11px] font-bold text-ink">{{ $belumDibaca }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Komunitas yang diikuti --}}
        <div class="rounded-4xl bg-white p-5">
            <div class="mb-3 flex items-baseline justify-between">
                <h2 class="text-sm font-bold">Komunitas saya</h2>
                <a href="{{ route('user.komunitas') }}" class="text-xs font-semibold text-forest hover:underline">Semua</a>
            </div>

            @forelse ($komunitasSaya as $k)
                <a href="{{ route('komunitas.show', $k->id_komunitas) }}"
                   class="-mx-2 flex items-center gap-3 rounded-2xl px-2 py-2 hover:bg-canvas">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-canvas text-xs font-bold">
                        {{ strtoupper(mb_substr($k->nama_komunitas, 0, 2)) }}
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold">{{ $k->nama_komunitas }}</span>
                        <span class="block text-xs text-muted">
                            {{ ($k->pivot->role ?? 'member') === 'leader' ? 'Leader' : 'Member' }}
                        </span>
                    </span>
                </a>
            @empty
                <p class="text-sm leading-relaxed text-muted">
                    Kamu belum bergabung ke komunitas mana pun.
                </p>
                <a href="{{ route('komunitas.index') }}"
                   class="mt-3 inline-flex rounded-full bg-forest px-4 py-2 text-xs font-semibold text-white hover:bg-forestdim">
                    Cari komunitas
                </a>
            @endforelse
        </div>

        {{-- Ajakan membuat komunitas --}}
        <div class="rounded-4xl bg-forest p-5 text-white">
            @include('partials.logo', ['teks' => false, 'ukuran' => 'h-9 w-auto'])
            <p class="mt-4 text-base font-bold leading-snug">Punya ide komunitas belajar?</p>
            <p class="mt-1.5 text-sm leading-relaxed text-white/75">
                Ajukan ke admin, dan kamu jadi leader komunitasnya.
            </p>
            <a href="{{ route('pengajuan.create') }}"
               class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-xs font-semibold text-ink hover:bg-lime">
                Ajukan komunitas
            </a>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 text-xs font-semibold text-muted hover:text-ink">Keluar dari akun</button>
        </form>
    </div>
</aside>

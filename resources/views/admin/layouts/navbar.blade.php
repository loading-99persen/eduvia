@php
    $admin      = auth()->user();
    $namaAdmin  = $admin?->profil?->nama_lengkap ?: 'Admin';
    $inisialAdm = strtoupper(mb_substr($namaAdmin, 0, 1));
@endphp

<header class="flex flex-wrap items-center justify-between gap-4 rounded-3xl bg-white px-6 py-5 shadow-sm sm:px-8">

    {{-- JUDUL --}}

    <div>
        <h2 class="text-2xl font-bold text-forest sm:text-3xl">
            @yield('judul', 'Dashboard Admin')
        </h2>

        <p class="mt-1 text-sm text-ink/60">
            @yield('subjudul', 'Kelola platform belajar Eduvia')
        </p>
    </div>

    {{-- PROFIL --}}

    <div class="flex items-center gap-4">
        <div class="grid h-12 w-12 place-items-center rounded-full bg-lime text-lg font-bold text-forest">
            {{ $inisialAdm }}
        </div>

        <div class="hidden sm:block">
            <p class="font-semibold text-ink">{{ $admin?->email ?? 'Admin' }}</p>
            <p class="text-sm text-muted">Administrator</p>
        </div>
    </div>

</header>

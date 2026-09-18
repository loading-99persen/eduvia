@extends('layouts.admin')

@section('title', 'Detail pengguna — Admin')

@section('judul', 'Detail Pengguna')
@section('subjudul', 'Profil, minat belajar, dan aktivitas akun.')

@section('content')

    @php
        $profil  = $user->profil;
        $nama    = $profil->nama_lengkap ?? 'Tanpa nama';
        $foto    = $profil && $profil->photo ? asset('storage/' . $profil->photo) : null;
        $inisial = strtoupper(mb_substr($nama, 0, 1));
    @endphp

    <a href="{{ route('admin.users') }}" class="mb-5 inline-flex rounded-full bg-white px-5 py-2.5 text-sm font-semibold hover:bg-line">
        &larr; Kembali ke daftar pengguna
    </a>

    <div class="grid gap-4 lg:grid-cols-[1.1fr_.9fr]">

        {{-- IDENTITAS --}}

        <section class="rounded-4xl bg-white p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-4">
                @if ($foto)
                    <img src="{{ $foto }}" alt="" class="h-20 w-20 rounded-full object-cover">
                @else
                    <span class="grid h-20 w-20 place-items-center rounded-full bg-lime text-2xl font-bold text-forest">{{ $inisial }}</span>
                @endif

                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">{{ $nama }}</h2>
                    <p class="text-sm text-muted">{{ $user->email }}</p>

                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->isAdmin() ? 'bg-forest text-lime' : 'bg-canvas text-muted' }}">
                            {{ $user->isAdmin() ? 'Admin' : 'User' }}
                        </span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->status === 'aktif' ? 'bg-limesoft text-forest' : 'bg-tangerine/20 text-ember' }}">
                            {{ $user->status }}
                        </span>
                    </div>
                </div>
            </div>

            <dl class="mt-7 grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['Jenjang pendidikan', $profil->tingkat_pendidikan ?? null],
                    ['Institusi', $profil->institusi ?? null],
                    ['Jenis kelamin', $profil->jenis_kelamin ?? null],
                    ['Tanggal lahir', $profil->tanggal_lahir ?? null],
                    ['Preferensi belajar', $profil->preferensi_belajar ?? null],
                    ['Media sosial', $profil->media_sosial ?? null],
                ] as [$label, $nilai])
                    <div class="rounded-2xl bg-canvas px-4 py-3">
                        <dt class="text-xs font-semibold text-muted">{{ $label }}</dt>
                        <dd class="mt-0.5 text-sm font-semibold">{{ $nilai ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if (!empty($profil?->bio))
                <div class="mt-4 rounded-2xl bg-canvas px-4 py-3">
                    <p class="text-xs font-semibold text-muted">Bio</p>
                    <p class="mt-1 max-w-[70ch] text-sm leading-relaxed">{{ $profil->bio }}</p>
                </div>
            @endif

            {{-- AKSI ADMIN --}}

            <div class="mt-6 flex flex-wrap gap-2 border-t border-line pt-6">
                <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $user->status === 'aktif' ? 'nonaktif' : 'aktif' }}">
                    <button class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">
                        {{ $user->status === 'aktif' ? 'Nonaktifkan akun' : 'Aktifkan akun' }}
                    </button>
                </form>

                <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="id_role" value="{{ $user->isAdmin() ? 2 : 1 }}">
                    <button class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">
                        Jadikan {{ $user->isAdmin() ? 'user' : 'admin' }}
                    </button>
                </form>

                <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST"
                      onsubmit="return confirm('Hapus akun ini beserta datanya?')">
                    @csrf @method('DELETE')
                    <button class="rounded-full px-5 py-2.5 text-sm font-semibold text-muted hover:text-ember">Hapus akun</button>
                </form>
            </div>
        </section>

        {{-- RINGKASAN AKTIVITAS --}}

        <div class="space-y-4">

            <section class="grid grid-cols-2 gap-4">
                @foreach ([
                    ['Postingan', $user->posts_count],
                    ['Komentar', $user->komentar_count],
                    ['Komunitas', $user->komunitas_count],
                    ['Webinar diikuti', $user->webinar_count],
                ] as [$label, $nilai])
                    <div class="rounded-4xl bg-white p-5">
                        <p class="text-3xl font-extrabold tracking-tight">{{ $nilai }}</p>
                        <p class="mt-1 text-xs text-muted">{{ $label }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-4xl bg-white p-6">
                <h3 class="text-sm font-bold">Minat belajar</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($user->minat as $m)
                        <span class="rounded-full bg-limesoft px-3 py-1 text-xs font-semibold text-forest">{{ $m->nama_minat }}</span>
                    @empty
                        <p class="text-sm text-muted">Onboarding study profile belum diisi.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-4xl bg-white p-6">
                <h3 class="text-sm font-bold">Komunitas yang diikuti</h3>
                <ul class="mt-3 divide-y divide-line">
                    @forelse ($user->komunitas as $k)
                        <li class="flex items-center gap-3 py-3">
                            <a href="{{ route('admin.komunitas.show', $k->id_komunitas) }}" class="text-sm font-semibold hover:underline">
                                {{ $k->nama_komunitas }}
                            </a>
                            <span class="ml-auto rounded-full px-3 py-1 text-xs font-bold {{ $k->pivot->role === 'leader' ? 'bg-forest text-lime' : 'bg-canvas text-muted' }}">
                                {{ $k->pivot->role === 'leader' ? 'Leader' : 'Member' }}
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">Belum bergabung ke komunitas mana pun.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-4xl bg-white p-6">
                <h3 class="text-sm font-bold">Postingan terbaru</h3>
                <ul class="mt-3 divide-y divide-line">
                    @forelse ($postTerbaru as $p)
                        <li class="py-3">
                            <a href="{{ route('post.show', $p->id_post) }}" class="text-sm hover:underline">
                                {{ \Illuminate\Support\Str::limit($p->konten, 90) }}
                            </a>
                            <p class="mt-0.5 text-xs text-muted">
                                {{ $p->komunitas->nama_komunitas ?? 'Tanpa komunitas' }} &middot;
                                {{ optional($p->dibuat_pada)->locale('id')->isoFormat('D MMM Y') }}
                            </p>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">Belum ada postingan.</li>
                    @endforelse
                </ul>
            </section>

        </div>
    </div>
@endsection

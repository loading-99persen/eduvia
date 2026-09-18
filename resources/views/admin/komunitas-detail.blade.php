@extends('layouts.admin')

@section('title', 'Detail komunitas — Admin')

@section('judul', 'Detail Komunitas')
@section('subjudul', 'Leader, anggota, diskusi, dan webinar komunitas.')

@section('content')

    <a href="{{ route('admin.komunitas') }}" class="mb-5 inline-flex rounded-full bg-white px-5 py-2.5 text-sm font-semibold hover:bg-line">
        &larr; Kembali ke daftar komunitas
    </a>

    <div class="grid gap-4 lg:grid-cols-[1.1fr_.9fr]">

        <section class="rounded-4xl bg-white p-6 sm:p-8">
            <div class="flex flex-wrap items-start gap-4">
                @if ($komunitas->gambar)
                    <img src="{{ asset('storage/' . $komunitas->gambar) }}" alt="" class="h-20 w-20 rounded-3xl object-cover">
                @else
                    <span class="grid h-20 w-20 place-items-center rounded-3xl bg-limesoft text-2xl font-bold text-forest">
                        {{ strtoupper(mb_substr($komunitas->nama_komunitas, 0, 1)) }}
                    </span>
                @endif

                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">{{ $komunitas->nama_komunitas }}</h2>
                    <p class="text-sm text-muted">{{ $komunitas->kategori ?: 'Tanpa kategori' }}</p>

                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $komunitas->status === 'aktif' ? 'bg-limesoft text-forest' : 'bg-tangerine/20 text-ember' }}">
                        {{ $komunitas->status }}
                    </span>
                </div>
            </div>

            <p class="mt-6 max-w-[70ch] text-[15px] leading-relaxed text-muted">
                {{ $komunitas->deskripsi ?: 'Komunitas ini belum menuliskan deskripsi.' }}
            </p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Leader</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        <a href="{{ route('admin.users.show', $komunitas->id_leader) }}" class="hover:underline">
                            {{ $komunitas->leader->profil->nama_lengkap ?? '-' }}
                        </a>
                    </dd>
                    <dd class="text-xs text-muted">{{ $komunitas->leader->email ?? '-' }}</dd>
                </div>

                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Dibuat pada</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        {{ optional($komunitas->dibuat_pada)->locale('id')->isoFormat('D MMMM Y') ?: '-' }}
                    </dd>
                </div>
            </dl>

            <div class="mt-6 flex flex-wrap gap-2 border-t border-line pt-6">
                <a href="{{ route('komunitas.show', $komunitas->id_komunitas) }}" target="_blank" rel="noopener"
                   class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Lihat sisi user</a>

                <form action="{{ route('admin.komunitas.update', $komunitas->id_komunitas) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $komunitas->status === 'aktif' ? 'nonaktif' : 'aktif' }}">
                    <button class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">
                        {{ $komunitas->status === 'aktif' ? 'Nonaktifkan komunitas' : 'Aktifkan komunitas' }}
                    </button>
                </form>

                <form action="{{ route('admin.komunitas.destroy', $komunitas->id_komunitas) }}" method="POST"
                      onsubmit="return confirm('Hapus komunitas ini beserta diskusinya?')">
                    @csrf @method('DELETE')
                    <button class="rounded-full px-5 py-2.5 text-sm font-semibold text-muted hover:text-ember">Hapus komunitas</button>
                </form>
            </div>
        </section>

        <div class="space-y-4">

            <section class="grid grid-cols-3 gap-4">
                @foreach ([
                    ['Anggota', $komunitas->member_komunitas_count],
                    ['Diskusi', $komunitas->posts_count],
                    ['Webinar', $komunitas->webinar_count],
                ] as [$label, $nilai])
                    <div class="rounded-4xl bg-white p-5">
                        <p class="text-3xl font-extrabold tracking-tight">{{ $nilai }}</p>
                        <p class="mt-1 text-xs text-muted">{{ $label }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-4xl bg-white p-6">
                <h3 class="text-sm font-bold">Member</h3>
                <ul class="mt-3 max-h-[420px] divide-y divide-line overflow-y-auto">
                    @forelse ($komunitas->memberKomunitas as $m)
                        <li class="flex items-center gap-3 py-3">
                            <div>
                                <a href="{{ route('admin.users.show', $m->id_user) }}" class="text-sm font-semibold hover:underline">
                                    {{ $m->user->profil->nama_lengkap ?? 'Tanpa nama' }}
                                </a>
                                <p class="text-xs text-muted">{{ $m->user->email ?? '-' }}</p>
                            </div>
                            <span class="ml-auto rounded-full px-3 py-1 text-xs font-bold {{ $m->role === 'leader' ? 'bg-forest text-lime' : 'bg-canvas text-muted' }}">
                                {{ $m->role === 'leader' ? 'Leader' : 'Member' }}
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">Belum ada anggota.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-4xl bg-white p-6">
                <h3 class="text-sm font-bold">Webinar komunitas</h3>
                <ul class="mt-3 divide-y divide-line">
                    @forelse ($komunitas->webinar as $w)
                        <li class="py-3">
                            <a href="{{ route('admin.webinar.show', $w->id_webinar) }}" class="text-sm font-semibold hover:underline">
                                {{ $w->judul }}
                            </a>
                            <p class="mt-0.5 text-xs text-muted">
                                {{ $w->mulai->locale('id')->isoFormat('D MMM Y, HH:mm') }} WIB &middot; {{ $w->label_status }}
                            </p>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">Belum ada webinar.</li>
                    @endforelse
                </ul>
            </section>

        </div>
    </div>
@endsection

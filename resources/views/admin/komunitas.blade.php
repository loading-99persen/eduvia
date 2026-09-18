@extends('layouts.admin')

@section('title', 'Komunitas — Admin')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Komunitas</h1>
        <p class="mt-3 text-[15px] text-muted">Komunitas nonaktif tidak muncul di halaman jelajah user.</p>
    </header>

    <form action="{{ route('admin.komunitas') }}" method="GET" class="mb-5 flex flex-wrap gap-2 rounded-4xl bg-white p-4">
        <label class="sr-only" for="cari">Cari komunitas</label>
        <input id="cari" name="cari" value="{{ $cari }}" placeholder="Cari nama komunitas"
               class="min-w-[220px] flex-1 rounded-full border-0 bg-canvas px-5 py-2.5 text-sm focus:ring-2 focus:ring-forest">
        <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Cari</button>
    </form>

    <div class="overflow-x-auto rounded-4xl bg-white">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="border-b border-line text-xs text-muted">
                <tr>
                    <th class="px-6 py-4 font-semibold">Komunitas</th>
                    <th class="px-6 py-4 font-semibold">Leader</th>
                    <th class="px-6 py-4 font-semibold">Isi</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($komunitas as $k)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('komunitas.show', $k->id_komunitas) }}" class="font-semibold hover:underline">
                                {{ $k->nama_komunitas }}
                            </a>
                            <p class="text-xs text-muted">{{ $k->kategori ?: 'Tanpa kategori' }}</p>
                        </td>
                        <td class="px-6 py-4 text-muted">{{ $k->leader->profil->nama_lengkap ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-muted">
                            {{ $k->member_komunitas_count }} anggota &middot; {{ $k->posts_count }} diskusi &middot; {{ $k->webinar_count }} webinar
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $k->status === 'aktif' ? 'bg-limesoft text-forest' : 'bg-tangerine/20 text-ember' }}">
                                {{ $k->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap justify-end gap-2">
                                <form action="{{ route('admin.komunitas.update', $k->id_komunitas) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $k->status === 'aktif' ? 'nonaktif' : 'aktif' }}">
                                    <button class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                                        {{ $k->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.komunitas.destroy', $k->id_komunitas) }}" method="POST"
                                      onsubmit="return confirm('Hapus komunitas ini beserta diskusinya?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-muted">Belum ada komunitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $komunitas->links() }}</div>
@endsection

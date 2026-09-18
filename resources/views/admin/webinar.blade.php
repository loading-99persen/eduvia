@extends('layouts.admin')

@section('title', 'Webinar — Admin')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Webinar</h1>
        <p class="mt-3 text-[15px] text-muted">Webinar dibuat dari pengajuan leader yang disetujui.</p>
    </header>

    <form action="{{ route('admin.webinar') }}" method="GET" class="mb-5 flex flex-wrap gap-2 rounded-4xl bg-white p-4">
        <label class="sr-only" for="cari">Cari webinar</label>
        <input id="cari" name="cari" value="{{ $cari }}" placeholder="Cari judul webinar"
               class="min-w-[220px] flex-1 rounded-full border-0 bg-canvas px-5 py-2.5 text-sm focus:ring-2 focus:ring-forest">
        <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Cari</button>
    </form>

    <div class="overflow-x-auto rounded-4xl bg-white">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="border-b border-line text-xs text-muted">
                <tr>
                    <th class="px-6 py-4 font-semibold">Webinar</th>
                    <th class="px-6 py-4 font-semibold">Komunitas</th>
                    <th class="px-6 py-4 font-semibold">Jadwal</th>
                    <th class="px-6 py-4 font-semibold">Peserta</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($webinar as $w)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('webinar.show', $w->id_webinar) }}" class="font-semibold hover:underline">{{ $w->judul }}</a>
                            <p class="text-xs text-muted">{{ $w->pembicara ?: '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-muted">{{ $w->komunitas->nama_komunitas ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-muted">
                            {{ $w->mulai->locale('id')->isoFormat('D MMM Y') }} &middot; <span data-wib="{{ $w->mulai->format('Y-m-d H:i') }}">{{ $w->mulai->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4">{{ $w->partisipasi_count }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-canvas px-3 py-1 text-xs font-bold text-muted">{{ $w->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <form action="{{ route('admin.webinar.update', $w->id_webinar) }}" method="POST" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <label class="sr-only" for="status-{{ $w->id_webinar }}">Status webinar</label>
                                    <select id="status-{{ $w->id_webinar }}" name="status"
                                            class="rounded-full border-0 bg-canvas px-4 py-2 text-xs font-semibold">
                                        @foreach (['akan_datang', 'berlangsung', 'selesai', 'dibatalkan'] as $s)
                                            <option value="{{ $s }}" @selected($w->status === $s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">Simpan</button>
                                </form>
                                <form action="{{ route('admin.webinar.destroy', $w->id_webinar) }}" method="POST"
                                      onsubmit="return confirm('Hapus webinar ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-muted">Belum ada webinar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $webinar->links() }}</div>
@endsection

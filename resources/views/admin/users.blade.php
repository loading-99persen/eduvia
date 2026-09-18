@extends('layouts.admin')

@section('title', 'Pengguna — Admin')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Pengguna</h1>
        <p class="mt-3 text-[15px] text-muted">Kelola status akun dan peran pengguna.</p>
    </header>

    <form action="{{ route('admin.users') }}" method="GET" class="mb-5 flex flex-wrap items-center gap-2 rounded-4xl bg-white p-4">
        <label class="sr-only" for="cari">Cari pengguna</label>
        <input id="cari" name="cari" value="{{ $cari }}" placeholder="Cari nama atau email"
               class="min-w-[220px] flex-1 rounded-full border-0 bg-canvas px-5 py-2.5 text-sm focus:ring-2 focus:ring-forest">

        <select name="status" class="rounded-full border-0 bg-canvas px-5 py-2.5 text-sm font-semibold">
            @foreach (['semua' => 'Semua status', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $k => $l)
                <option value="{{ $k }}" @selected($status === $k)>{{ $l }}</option>
            @endforeach
        </select>

        <select name="role" class="rounded-full border-0 bg-canvas px-5 py-2.5 text-sm font-semibold">
            @foreach (['semua' => 'Semua peran', 'user' => 'User', 'admin' => 'Admin'] as $k => $l)
                <option value="{{ $k }}" @selected($role === $k)>{{ $l }}</option>
            @endforeach
        </select>

        <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Terapkan</button>
    </form>

    <div class="overflow-x-auto rounded-4xl bg-white">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="border-b border-line text-xs text-muted">
                <tr>
                    <th class="px-6 py-4 font-semibold">Nama</th>
                    <th class="px-6 py-4 font-semibold">Email</th>
                    <th class="px-6 py-4 font-semibold">Peran</th>
                    <th class="px-6 py-4 font-semibold">Aktivitas</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($users as $u)
                    <tr>
                        <td class="px-6 py-4 font-semibold">{{ $u->profil->nama_lengkap ?? 'Tanpa nama' }}</td>
                        <td class="px-6 py-4 text-muted">{{ $u->email }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $u->isAdmin() ? 'bg-forest text-lime' : 'bg-canvas text-muted' }}">
                                {{ $u->isAdmin() ? 'Admin' : 'User' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-muted">
                            {{ $u->posts_count }} postingan &middot; {{ $u->komunitas_count }} komunitas
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $u->status === 'aktif' ? 'bg-limesoft text-forest' : 'bg-tangerine/20 text-ember' }}">
                                {{ $u->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap justify-end gap-2">
                                <form action="{{ route('admin.users.update', $u->id_user) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $u->status === 'aktif' ? 'nonaktif' : 'aktif' }}">
                                    <button class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                                        {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.update', $u->id_user) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="id_role" value="{{ $u->isAdmin() ? 2 : 1 }}">
                                    <button class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                                        Jadikan {{ $u->isAdmin() ? 'user' : 'admin' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.destroy', $u->id_user) }}" method="POST"
                                      onsubmit="return confirm('Hapus akun ini beserta datanya?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-muted">Tidak ada pengguna yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection

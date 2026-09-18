@extends('layouts.admin')

@section('title', 'Detail webinar — Admin')

@section('judul', 'Detail Webinar')
@section('subjudul', 'Data webinar, leader pembuat, dan daftar pesertanya.')

@section('content')

    <a href="{{ route('admin.webinar') }}" class="mb-5 inline-flex rounded-full bg-white px-5 py-2.5 text-sm font-semibold hover:bg-line">
        &larr; Kembali ke daftar webinar
    </a>

    <div class="grid gap-4 lg:grid-cols-[1.1fr_.9fr]">

        <section class="rounded-4xl bg-white p-6 sm:p-8">
            @if ($webinar->foto)
                <img src="{{ asset('storage/' . $webinar->foto) }}" alt="" class="mb-6 h-48 w-full rounded-3xl object-cover">
            @endif

            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-canvas px-3 py-1 text-xs font-bold text-muted">{{ $webinar->label_status }}</span>
                <span class="rounded-full bg-limesoft px-3 py-1 text-xs font-semibold text-forest">
                    {{ $webinar->kategori ?: 'Tanpa kategori' }}
                </span>
            </div>

            <h2 class="mt-4 text-2xl font-extrabold tracking-tight">{{ $webinar->judul }}</h2>

            <p class="mt-4 max-w-[70ch] text-[15px] leading-relaxed text-muted">
                {{ $webinar->deskripsi ?: 'Tidak ada deskripsi.' }}
            </p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Komunitas penyelenggara</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        @if ($webinar->komunitas)
                            <a href="{{ route('admin.komunitas.show', $webinar->id_komunitas) }}" class="hover:underline">
                                {{ $webinar->komunitas->nama_komunitas }}
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                </div>

                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Leader pembuat</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        <a href="{{ route('admin.users.show', $webinar->id_leader) }}" class="hover:underline">
                            {{ $webinar->leader->profil->nama_lengkap ?? '-' }}
                        </a>
                    </dd>
                    <dd class="text-xs text-muted">{{ $webinar->leader->email ?? '-' }}</dd>
                </div>

                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Jadwal</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        {{ $webinar->mulai->locale('id')->isoFormat('D MMMM Y') }} &middot;
                        {{ $webinar->mulai->format('H:i') }} WIB
                    </dd>
                </div>

                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Pembicara</dt>
                    <dd class="mt-0.5 text-sm font-semibold">{{ $webinar->pembicara ?: '-' }}</dd>
                </div>
            </dl>

            <div class="mt-4 rounded-2xl bg-canvas px-4 py-3">
                <p class="text-xs font-semibold text-muted">Link meeting</p>
                <p class="mt-0.5 break-all text-sm font-semibold">{{ $webinar->link_meeting ?: 'Belum diisi leader.' }}</p>
                <p class="mt-1 text-xs text-muted">
                    Link hanya terbuka untuk peserta terdaftar, mulai {{ \App\Models\Webinar::MENIT_AKSES_AWAL }} menit sebelum acara.
                </p>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-line pt-6">
                <form action="{{ route('admin.webinar.update', $webinar->id_webinar) }}" method="POST" class="flex flex-wrap gap-2">
                    @csrf @method('PUT')
                    <label class="sr-only" for="status-webinar">Status webinar</label>
                    <select id="status-webinar" name="status" class="rounded-full border-0 bg-canvas px-5 py-2.5 text-sm font-semibold">
                        @foreach (['akan_datang', 'berlangsung', 'selesai', 'dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected($webinar->status === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Simpan status</button>
                </form>

                <form action="{{ route('admin.webinar.destroy', $webinar->id_webinar) }}" method="POST"
                      onsubmit="return confirm('Hapus webinar ini?')">
                    @csrf @method('DELETE')
                    <button class="rounded-full px-5 py-2.5 text-sm font-semibold text-muted hover:text-ember">Hapus webinar</button>
                </form>
            </div>
        </section>

        {{-- PESERTA --}}

        <section class="rounded-4xl bg-white p-6">
            <div class="flex items-center gap-3">
                <h3 class="text-sm font-bold">Peserta terdaftar</h3>
                <span class="ml-auto rounded-full bg-limesoft px-3 py-1 text-xs font-bold text-forest">
                    {{ $webinar->partisipasi_count }} orang
                </span>
            </div>

            <p class="mt-2 text-xs text-muted">
                Data peserta diambil otomatis dari akun dan profil user, tanpa formulir tambahan.
            </p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[520px] text-left text-sm">
                    <thead class="border-b border-line text-xs text-muted">
                        <tr>
                            <th class="py-3 pr-4 font-semibold">Nama</th>
                            <th class="py-3 pr-4 font-semibold">Institusi</th>
                            <th class="py-3 font-semibold">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($webinar->partisipasi as $p)
                            <tr>
                                <td class="py-3 pr-4">
                                    <a href="{{ route('admin.users.show', $p->id_user) }}" class="font-semibold hover:underline">
                                        {{ $p->user->profil->nama_lengkap ?? 'Tanpa nama' }}
                                    </a>
                                    <p class="text-xs text-muted">{{ $p->user->email ?? '-' }}</p>
                                </td>
                                <td class="py-3 pr-4 text-xs text-muted">
                                    {{ $p->user->profil->institusi ?? '-' }}
                                    <br>
                                    {{ $p->user->profil->tingkat_pendidikan ?? '-' }}
                                </td>
                                <td class="py-3 text-xs text-muted">
                                    {{ optional($p->bergabung_pada)->locale('id')->isoFormat('D MMM Y') ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-muted">Belum ada peserta.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
@endsection

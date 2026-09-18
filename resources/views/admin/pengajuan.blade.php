@extends('layouts.admin')

@section('title', 'Pengajuan — Admin')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Pengajuan</h1>
        <p class="mt-3 max-w-[60ch] text-[15px] leading-relaxed text-muted">
            Menyetujui pengajuan komunitas otomatis membuat komunitas, group chat, dan menjadikan pengaju sebagai leader.
        </p>
    </header>

    <div class="mb-5 flex flex-wrap gap-2 rounded-4xl bg-white p-3">
        @foreach (['proses' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
            <a href="{{ route('admin.pengajuan', ['status' => $key, 'tipe' => $tipe]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === $key ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                {{ $label }}
            </a>
        @endforeach
        <span class="mx-2 w-px bg-line"></span>
        @foreach (['semua' => 'Semua tipe', 'komunitas' => 'Komunitas', 'webinar' => 'Webinar'] as $key => $label)
            <a href="{{ route('admin.pengajuan', ['status' => $status, 'tipe' => $key]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold {{ $tipe === $key ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($pengajuan as $p)
            <article class="rounded-4xl bg-white p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">
                        {{ $p->tipe === 'webinar' ? 'Webinar' : 'Komunitas' }}
                    </span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold
                        {{ $p->status === 'disetujui' ? 'bg-forest text-lime' : ($p->status === 'ditolak' ? 'bg-tangerine text-ink' : 'bg-sun text-ink') }}">
                        {{ $p->label_status }}
                    </span>
                    <span class="ml-auto text-xs text-muted">
                        {{ optional($p->tanggal_pengajuan)->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                    </span>
                </div>

                <h2 class="mt-3 text-lg font-extrabold tracking-tight">{{ $p->judul }}</h2>
                <p class="mt-1 text-xs text-muted">
                    Diajukan oleh {{ $p->user->profil->nama_lengkap ?? '-' }} ({{ $p->user->email ?? '-' }})
                    @if ($p->kategori) &middot; kategori {{ $p->kategori }} @endif
                    @if ($p->komunitas) &middot; komunitas {{ $p->komunitas->nama_komunitas }} @endif
                </p>

                <p class="mt-3 max-w-[70ch] text-sm leading-relaxed">{{ $p->deskripsi }}</p>

                @if ($p->alasan)
                    <div class="mt-3 rounded-2xl bg-canvas p-4">
                        <p class="text-xs font-bold">Alasan pengaju</p>
                        <p class="mt-1 text-sm leading-relaxed text-muted">{{ $p->alasan }}</p>
                    </div>
                @endif

                @if ($p->tipe === 'webinar')
                    <p class="mt-3 text-sm text-muted">
                        Jadwal: {{ optional($p->tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                        {{ $p->waktu ? substr($p->waktu, 0, 5) . ' WIB' : '' }} &middot;
                        Pembicara {{ $p->pembicara ?: '-' }}
                        @if ($p->link_meeting) &middot; <a href="{{ $p->link_meeting }}" class="text-forest hover:underline" target="_blank" rel="noopener">tautan meeting</a> @endif
                    </p>
                @endif

                @if ($p->catatan_admin)
                    <div class="mt-3 rounded-2xl border border-tangerine bg-tangerine/10 p-4">
                        <p class="text-xs font-bold">Catatan penolakan</p>
                        <p class="mt-1 text-sm leading-relaxed">{{ $p->catatan_admin }}</p>
                    </div>
                @endif

                @if ($p->status === 'proses')
                    <div class="mt-5 flex flex-wrap items-start gap-3 border-t border-line pt-5">
                        <form action="{{ route('admin.pengajuan.terima', $p->id_pengajuan) }}" method="POST">
                            @csrf @method('PUT')
                            <button class="rounded-full bg-forest px-6 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Setujui</button>
                        </form>

                        <form action="{{ route('admin.pengajuan.tolak', $p->id_pengajuan) }}" method="POST"
                              class="flex min-w-[280px] flex-1 flex-wrap gap-2">
                            @csrf @method('PUT')
                            <label class="sr-only" for="catatan-{{ $p->id_pengajuan }}">Alasan penolakan</label>
                            <input id="catatan-{{ $p->id_pengajuan }}" name="catatan_admin" required minlength="10"
                                   placeholder="Alasan penolakan (minimal 10 karakter)"
                                   class="min-w-[220px] flex-1 rounded-2xl border-0 bg-canvas px-4 py-2.5 text-sm focus:ring-2 focus:ring-forest">
                            <button class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Tolak</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center text-sm text-muted">Tidak ada pengajuan pada filter ini.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $pengajuan->links() }}</div>
@endsection

@extends('layouts.admin')

@section('title', 'Laporan — Admin')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Laporan konten</h1>
        <p class="mt-3 text-[15px] text-muted">Tinjau laporan dari pengguna, lalu tindak lanjuti di halaman Moderasi.</p>
    </header>

    <div class="mb-5 flex flex-wrap gap-2 rounded-4xl bg-white p-3">
        @foreach (['proses' => 'Menunggu', 'diterima' => 'Ditindaklanjuti', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $k => $l)
            <a href="{{ route('admin.laporan', ['status' => $k]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === $k ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                {{ $l }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($reports as $r)
            @php $target = $r->target(); @endphp
            <article class="rounded-4xl bg-white p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">
                        {{ $r->tipe_target }} #{{ $r->id_target }}
                    </span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold
                        {{ $r->status === 'diterima' ? 'bg-forest text-lime' : ($r->status === 'ditolak' ? 'bg-canvas text-muted' : 'bg-sun text-ink') }}">
                        {{ $r->status }}
                    </span>
                    <span class="ml-auto text-xs text-muted">
                        Pelapor: {{ $r->user->profil->nama_lengkap ?? '-' }} &middot;
                        {{ optional($r->dibuat_pada)->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                    </span>
                </div>

                <p class="mt-4 max-w-[70ch] text-[15px] leading-relaxed">{{ $r->alasan }}</p>

                @if ($target)
                    <div class="mt-4 rounded-2xl bg-canvas p-4">
                        <p class="text-xs font-bold">Konten yang dilaporkan</p>
                        <p class="mt-1 max-w-[70ch] text-sm leading-relaxed text-muted">
                            {{ \Illuminate\Support\Str::limit($target->konten ?? $target->komentar ?? $target->nama_komunitas ?? $target->judul ?? $target->email ?? '-', 240) }}
                        </p>

                        @if ($r->tipe_target === 'post')
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('post.show', $r->id_target) }}" target="_blank" rel="noopener"
                                   class="rounded-full bg-white px-4 py-2 text-xs font-semibold hover:bg-line">Buka postingan</a>
                                <form action="{{ route('admin.moderasi.post', $r->id_target) }}" method="POST"
                                      onsubmit="return confirm('Hapus postingan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus postingan</button>
                                </form>
                            </div>
                        @elseif ($r->tipe_target === 'komentar')
                            <form action="{{ route('admin.moderasi.komentar', $r->id_target) }}" method="POST" class="mt-3"
                                  onsubmit="return confirm('Hapus komentar ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-full bg-white px-4 py-2 text-xs font-semibold hover:text-ember">Hapus komentar</button>
                            </form>
                        @endif
                    </div>
                @else
                    <p class="mt-4 text-sm text-muted">Konten sudah tidak tersedia (mungkin telah dihapus).</p>
                @endif

                <form action="{{ route('admin.laporan.update', $r->id_reports) }}" method="POST"
                      class="mt-5 flex flex-wrap items-center gap-2 border-t border-line pt-5">
                    @csrf @method('PUT')
                    <label class="sr-only" for="tanggapan-{{ $r->id_reports }}">Tanggapan admin</label>
                    <input id="tanggapan-{{ $r->id_reports }}" name="tanggapan_admin" value="{{ $r->tanggapan_admin }}"
                           placeholder="Tanggapan untuk pelapor (opsional)"
                           class="min-w-[220px] flex-1 rounded-2xl border-0 bg-canvas px-4 py-2.5 text-sm focus:ring-2 focus:ring-forest">
                    <label class="sr-only" for="status-{{ $r->id_reports }}">Status laporan</label>
                    <select id="status-{{ $r->id_reports }}" name="status"
                            class="rounded-full border-0 bg-canvas px-4 py-2.5 text-sm font-semibold">
                        @foreach (['proses', 'diterima', 'ditolak'] as $s)
                            <option value="{{ $s }}" @selected($r->status === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Simpan</button>
                </form>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-10 text-center text-sm text-muted">Tidak ada laporan pada filter ini.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $reports->links() }}</div>
@endsection

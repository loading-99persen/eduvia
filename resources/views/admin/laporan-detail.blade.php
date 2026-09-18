@extends('layouts.admin')

@section('title', 'Detail laporan — Admin')

@section('judul', 'Detail Laporan')
@section('subjudul', 'Isi laporan, konten yang dilaporkan, dan tindak lanjutnya.')

@section('content')

    <a href="{{ route('admin.laporan') }}" class="mb-5 inline-flex rounded-full bg-white px-5 py-2.5 text-sm font-semibold hover:bg-line">
        &larr; Kembali ke daftar laporan
    </a>

    <div class="grid gap-4 lg:grid-cols-[1.1fr_.9fr]">

        <section class="rounded-4xl bg-white p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">
                    {{ $report->tipe_target }} #{{ $report->id_target }}
                </span>
                <span class="rounded-full px-3 py-1 text-xs font-bold
                    {{ $report->status === 'diterima' ? 'bg-forest text-lime' : ($report->status === 'ditolak' ? 'bg-canvas text-muted' : 'bg-sun text-ink') }}">
                    {{ $report->label_status }}
                </span>
            </div>

            <h2 class="mt-4 text-xl font-extrabold tracking-tight">Alasan pelaporan</h2>
            <p class="mt-2 max-w-[70ch] text-[15px] leading-relaxed">{{ $report->alasan }}</p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Pelapor</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        <a href="{{ route('admin.users.show', $report->id_user) }}" class="hover:underline">
                            {{ $report->user->profil->nama_lengkap ?? '-' }}
                        </a>
                    </dd>
                    <dd class="text-xs text-muted">{{ $report->user->email ?? '-' }}</dd>
                </div>

                <div class="rounded-2xl bg-canvas px-4 py-3">
                    <dt class="text-xs font-semibold text-muted">Dilaporkan pada</dt>
                    <dd class="mt-0.5 text-sm font-semibold">
                        {{ optional($report->dibuat_pada)->locale('id')->isoFormat('D MMMM Y, HH:mm') ?: '-' }}
                    </dd>
                </div>
            </dl>

            <form action="{{ route('admin.laporan.update', $report->id_reports) }}" method="POST"
                  class="mt-6 border-t border-line pt-6">
                @csrf @method('PUT')

                <label for="tanggapan_admin" class="mb-1.5 block text-sm font-semibold">Tanggapan untuk pelapor</label>
                <textarea id="tanggapan_admin" name="tanggapan_admin" rows="3"
                          class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest"
                          placeholder="Opsional">{{ $report->tanggapan_admin }}</textarea>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <label class="sr-only" for="status-laporan">Status laporan</label>
                    <select id="status-laporan" name="status" class="rounded-full border-0 bg-canvas px-5 py-2.5 text-sm font-semibold">
                        @foreach (['proses' => 'Menunggu ditinjau', 'diterima' => 'Ditindaklanjuti', 'ditolak' => 'Ditolak'] as $k => $l)
                            <option value="{{ $k }}" @selected($report->status === $k)>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Simpan</button>
                </div>
            </form>
        </section>

        <section class="rounded-4xl bg-white p-6">
            <h3 class="text-sm font-bold">Konten yang dilaporkan</h3>

            @if ($target)
                <p class="mt-3 max-w-[70ch] text-sm leading-relaxed text-muted">
                    {{ \Illuminate\Support\Str::limit($target->konten ?? $target->komentar ?? $target->nama_komunitas ?? $target->email ?? '-', 400) }}
                </p>

                <div class="mt-5 flex flex-wrap gap-2 border-t border-line pt-5">
                    @if ($report->tipe_target === 'post')
                        <a href="{{ route('post.show', $report->id_target) }}" target="_blank" rel="noopener"
                           class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Buka postingan</a>

                        <form action="{{ route('admin.moderasi.post', $report->id_target) }}" method="POST"
                              onsubmit="return confirm('Hapus postingan ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-full px-5 py-2.5 text-sm font-semibold text-muted hover:text-ember">Hapus postingan</button>
                        </form>
                    @elseif ($report->tipe_target === 'komentar')
                        <form action="{{ route('admin.moderasi.komentar', $report->id_target) }}" method="POST"
                              onsubmit="return confirm('Hapus komentar ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:text-ember">Hapus komentar</button>
                        </form>
                    @elseif ($report->tipe_target === 'komunitas')
                        <a href="{{ route('admin.komunitas.show', $report->id_target) }}"
                           class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Lihat komunitas</a>
                    @elseif ($report->tipe_target === 'user')
                        <a href="{{ route('admin.users.show', $report->id_target) }}"
                           class="rounded-full bg-canvas px-5 py-2.5 text-sm font-semibold hover:bg-line">Lihat pengguna</a>
                    @endif
                </div>
            @else
                <p class="mt-3 text-sm text-muted">Konten sudah tidak tersedia, mungkin sudah dihapus.</p>
            @endif
        </section>

    </div>
@endsection

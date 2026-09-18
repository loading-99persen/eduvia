@extends('layouts.user')

@section('title', $komunitas->nama_komunitas . ' — Eduvia')

@section('content')

    {{-- Kepala komunitas --}}
    <section class="mb-6 overflow-hidden rounded-4xl bg-forest text-white">
        <div class="grid gap-8 p-7 md:grid-cols-[1.2fr_.8fr] md:p-9">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">{{ $komunitas->kategori ?: 'Umum' }}</span>
                    @if ($komunitas->status !== 'aktif')
                        <span class="inline-flex rounded-full bg-tangerine px-3 py-1 text-xs font-bold text-ink">Nonaktif</span>
                    @endif
                </div>

                <h1 class="headline mt-4 text-[38px] font-extrabold md:text-[46px]">{{ $komunitas->nama_komunitas }}</h1>
                <p class="mt-4 max-w-[52ch] text-[15px] leading-relaxed text-white/75">{{ $komunitas->deskripsi }}</p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if ($sudahGabung)
                        <span class="rounded-full bg-lime px-5 py-2.5 text-sm font-semibold text-ink">Kamu anggota di sini</span>
                        <a href="{{ route('komunitas.chat', $komunitas->id_komunitas) }}"
                           class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-ink hover:bg-lime">
                            Masuk group chat
                        </a>

                        @if ($sayaLeader)
                            <a href="{{ route('komunitas.edit', $komunitas->id_komunitas) }}"
                               class="rounded-full border border-white/35 px-5 py-2.5 text-sm font-semibold hover:bg-white/10">
                                Kelola komunitas
                            </a>
                        @else
                            <form action="{{ route('komunitas.keluar', $komunitas->id_komunitas) }}" method="POST"
                                  onsubmit="return confirm('Keluar dari komunitas ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-full border border-white/35 px-5 py-2.5 text-sm font-semibold hover:bg-white/10">
                                    Keluar komunitas
                                </button>
                            </form>
                        @endif
                    @else
                        <form action="{{ route('komunitas.gabung', $komunitas->id_komunitas) }}" method="POST">
                            @csrf
                            <button class="rounded-full bg-lime px-6 py-2.5 text-sm font-semibold text-ink hover:bg-white">
                                Gabung komunitas
                            </button>
                        </form>
                        <p class="text-sm text-white/70">Group chat terbuka setelah kamu bergabung.</p>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-3 self-start">
                <div class="rounded-3xl bg-white/10 p-5">
                    <dt class="text-xs text-white/70">Anggota</dt>
                    <dd class="mt-1 text-2xl font-extrabold">{{ $komunitas->member_komunitas_count }}</dd>
                </div>
                <div class="rounded-3xl bg-white/10 p-5">
                    <dt class="text-xs text-white/70">Diskusi</dt>
                    <dd class="mt-1 text-2xl font-extrabold">{{ $komunitas->posts_count }}</dd>
                </div>
                <div class="col-span-2 rounded-3xl bg-white/10 p-5">
                    <dt class="text-xs text-white/70">Leader</dt>
                    <dd class="mt-1 text-base font-bold">{{ $komunitas->leader->profil->nama_lengkap ?? 'Belum ditentukan' }}</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- Tab --}}
    <div class="mb-5 flex gap-1 rounded-full bg-white p-1">
        @foreach (['diskusi' => 'Diskusi', 'member' => 'Member', 'webinar' => 'Webinar'] as $key => $label)
            <a href="{{ route('komunitas.show', ['id' => $komunitas->id_komunitas, 'tab' => $key]) }}"
               class="flex-1 rounded-full px-4 py-2.5 text-center text-sm font-semibold transition
                      {{ $tab === $key ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($tab === 'diskusi')

        @if ($sudahGabung)
            <section class="mb-4 rounded-4xl bg-white p-6">
                <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_komunitas" value="{{ $komunitas->id_komunitas }}">
                    <input type="hidden" name="kembali" value="{{ route('komunitas.show', $komunitas->id_komunitas) }}">
                    <label class="sr-only" for="konten-komunitas">Tulis diskusi</label>
                    <textarea id="konten-komunitas" name="konten" rows="2" required
                              placeholder="Tulis pertanyaan atau bagikan sesuatu ke {{ $komunitas->nama_komunitas }}…"
                              class="w-full resize-none border-0 bg-transparent p-0 text-[15px] leading-relaxed placeholder:text-muted focus:ring-0"></textarea>
                    <div class="mt-4 flex items-center gap-2 border-t border-line pt-4">
                        <label class="cursor-pointer rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                            Gambar <input type="file" name="gambar" accept="image/*" class="hidden">
                        </label>
                        <label class="cursor-pointer rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">
                            File <input type="file" name="file" class="hidden">
                        </label>
                        <button class="ml-auto rounded-full bg-forest px-6 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Kirim</button>
                    </div>
                </form>
            </section>
        @endif

        <div class="space-y-4">
            @forelse ($posts as $post)
                @include('partials.post-card', ['post' => $post])
            @empty
                <div class="rounded-4xl bg-white p-10 text-center">
                    <p class="text-lg font-bold">Diskusi masih kosong</p>
                    <p class="mx-auto mt-2 max-w-[42ch] text-sm leading-relaxed text-muted">
                        Mulai dengan satu pertanyaan. Biasanya itu yang membuat anggota lain ikut bicara.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $posts->appends(['tab' => 'diskusi'])->links() }}</div>

    @elseif ($tab === 'member')

        <section class="rounded-4xl bg-white p-6">
            <h2 class="mb-4 text-sm font-bold">{{ $anggota->count() }} anggota</h2>
            <ul class="divide-y divide-line">
                @foreach ($anggota as $a)
                    @php $p = $a->user->profil ?? null; @endphp
                    <li class="flex items-center gap-3 py-3">
                        <a href="{{ route('user.show', $a->id_user) }}" class="shrink-0">
                            @if ($p?->photo)
                                <img src="{{ asset('storage/' . $p->photo) }}" alt="" class="h-11 w-11 rounded-full object-cover">
                            @else
                                <span class="grid h-11 w-11 place-items-center rounded-full bg-canvas text-sm font-bold">
                                    {{ strtoupper(mb_substr($p->nama_lengkap ?? 'P', 0, 1)) }}
                                </span>
                            @endif
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('user.show', $a->id_user) }}" class="block truncate text-sm font-semibold hover:underline">
                                {{ $p->nama_lengkap ?? 'Pengguna Eduvia' }}
                            </a>
                            <p class="truncate text-xs text-muted">{{ $p->institusi ?? '—' }}</p>
                        </div>
                        @if ($a->role === 'leader')
                            <span class="rounded-full bg-lime px-3 py-1 text-xs font-bold text-ink">Leader</span>
                        @else
                            <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">Member</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>

    @else

        @if ($sayaLeader)
            <div class="mb-4 flex flex-wrap items-center gap-4 rounded-4xl bg-white p-6">
                <div>
                    <p class="text-sm font-bold">Mau mengadakan webinar?</p>
                    <p class="mt-1 max-w-[46ch] text-sm leading-relaxed text-muted">
                        Sebagai leader, kamu bisa mengajukan webinar untuk komunitas ini. Admin akan meninjaunya lebih dulu.
                    </p>
                </div>
                <a href="{{ route('pengajuan.create', ['tipe' => 'webinar', 'komunitas' => $komunitas->id_komunitas]) }}"
                   class="ml-auto rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                    Ajukan webinar
                </a>
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($webinarKomunitas as $w)
                <article class="flex flex-wrap items-center gap-5 rounded-4xl bg-white p-6">
                    <div class="grid h-16 w-16 shrink-0 place-items-center rounded-3xl bg-limesoft text-center leading-none">
                        <span>
                            <span class="block text-xl font-extrabold">{{ $w->mulai->format('d') }}</span>
                            <span class="block text-[10px] font-semibold text-muted">{{ $w->mulai->locale('id')->isoFormat('MMM') }}</span>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-extrabold tracking-tight">{{ $w->judul }}</h3>
                            <span class="rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-muted">{{ $w->label_status }}</span>
                        </div>
                        <p class="mt-1 text-sm text-muted">
                            {{ $w->mulai->locale('id')->isoFormat('D MMMM Y') }} &middot; <span data-wib="{{ $w->mulai->format('Y-m-d H:i') }}">{{ $w->mulai->format('H:i') }} WIB</span>
                            &middot; Pembicara {{ $w->pembicara ?: '-' }}
                            &middot; {{ $w->partisipasi_count }} peserta
                        </p>
                    </div>
                    <a href="{{ route('webinar.show', $w->id_webinar) }}"
                       class="rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Lihat detail</a>
                </article>
            @empty
                <div class="rounded-4xl bg-white p-10 text-center">
                    <p class="text-lg font-bold">Belum ada webinar dijadwalkan</p>
                    <p class="mx-auto mt-2 max-w-[44ch] text-sm leading-relaxed text-muted">
                        Webinar komunitas dibuat oleh leader dan tayang setelah disetujui admin.
                    </p>
                </div>
            @endforelse
        </div>
    @endif
@endsection

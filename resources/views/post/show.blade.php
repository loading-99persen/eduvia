@extends('layouts.user')

@section('title', 'Diskusi — Eduvia')

@section('content')

    <a href="{{ $post->komunitas ? route('komunitas.show', $post->id_komunitas) : route('beranda') }}"
       class="mb-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold hover:bg-line">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M15 5 8 12l7 7"/>
        </svg>
        Kembali
    </a>

    @include('partials.post-card', ['post' => $post])

    {{-- Tulis komentar --}}
    <section class="mt-4 rounded-4xl bg-white p-6">
        <form action="{{ route('post.komentar', $post->id_post) }}" method="POST">
            @csrf
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-limesoft text-sm font-bold">
                    {{ strtoupper(mb_substr(auth()->user()->nama, 0, 1)) }}
                </span>
                <div class="flex-1">
                    <label class="sr-only" for="komentar">Tulis balasan</label>
                    <textarea id="komentar" name="komentar" rows="2" required
                              placeholder="Tulis balasan yang membantu…"
                              class="w-full resize-none rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed placeholder:text-muted focus:ring-2 focus:ring-forest">{{ old('komentar') }}</textarea>
                    <button class="mt-3 rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">Balas</button>
                </div>
            </div>
        </form>
    </section>

    <h2 class="mb-3 mt-8 text-sm font-bold">{{ $post->komentar_count }} balasan</h2>

    <div class="space-y-3">
        @forelse ($komentar as $k)
            @php $p = $k->user->profil ?? null; @endphp
            <article class="rounded-4xl bg-white p-6">
                <div class="flex items-start gap-3">
                    <a href="{{ route('user.show', $k->id_user) }}" class="shrink-0">
                        @if ($p?->photo)
                            <img src="{{ asset('storage/' . $p->photo) }}" alt="" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-canvas text-sm font-bold">
                                {{ strtoupper(mb_substr($p->nama_lengkap ?? 'P', 0, 1)) }}
                            </span>
                        @endif
                    </a>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2">
                            <a href="{{ route('user.show', $k->id_user) }}" class="text-sm font-bold hover:underline">
                                {{ $p->nama_lengkap ?? 'Pengguna Eduvia' }}
                            </a>
                            @if ($p?->institusi)
                                <span class="text-xs text-muted">{{ $p->institusi }}</span>
                            @endif
                            <span class="text-xs text-muted">&middot; {{ $k->dibuat_pada?->diffForHumans() }}</span>
                        </div>

                        <p class="mt-2 max-w-[62ch] whitespace-pre-line text-[15px] leading-relaxed">{{ $k->komentar }}</p>

                        <div class="mt-3 flex items-center gap-2">
                            <button type="button" onclick="document.getElementById('balas-{{ $k->id_komen }}').classList.toggle('hidden')"
                                    class="rounded-full px-3 py-1.5 text-xs font-semibold text-muted hover:bg-canvas hover:text-ink">
                                Balas
                            </button>

                            @if ($k->milikSaya())
                                <form action="{{ route('komentar.destroy', $k->id_komen) }}" method="POST"
                                      onsubmit="return confirm('Hapus komentar ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-full px-3 py-1.5 text-xs font-semibold text-muted hover:bg-canvas hover:text-ember">Hapus</button>
                                </form>
                            @else
                                <a href="{{ route('report.create', ['tipe' => 'komentar', 'id' => $k->id_komen, 'kembali' => url()->current()]) }}"
                                   class="rounded-full px-3 py-1.5 text-xs font-semibold text-muted hover:bg-canvas hover:text-ember">Laporkan</a>
                            @endif
                        </div>

                        {{-- Form balasan --}}
                        <form id="balas-{{ $k->id_komen }}" action="{{ route('post.komentar', $post->id_post) }}" method="POST" class="mt-3 hidden">
                            @csrf
                            <input type="hidden" name="id_parent" value="{{ $k->id_komen }}">
                            <textarea name="komentar" rows="2" required placeholder="Balas {{ $p->nama_lengkap ?? 'komentar ini' }}…"
                                      class="w-full resize-none rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest"></textarea>
                            <button class="mt-2 rounded-full bg-forest px-4 py-2 text-xs font-semibold text-white hover:bg-forestdim">Kirim balasan</button>
                        </form>

                        {{-- Balasan --}}
                        @if ($k->balasan->isNotEmpty())
                            <ul class="mt-4 space-y-3 border-l-2 border-line pl-4">
                                @foreach ($k->balasan as $b)
                                    @php $pb = $b->user->profil ?? null; @endphp
                                    <li>
                                        <div class="flex flex-wrap items-center gap-x-2">
                                            <a href="{{ route('user.show', $b->id_user) }}" class="text-sm font-bold hover:underline">
                                                {{ $pb->nama_lengkap ?? 'Pengguna' }}
                                            </a>
                                            <span class="text-xs text-muted">&middot; {{ $b->dibuat_pada?->diffForHumans() }}</span>
                                            @if ($b->milikSaya())
                                                <form action="{{ route('komentar.destroy', $b->id_komen) }}" method="POST"
                                                      onsubmit="return confirm('Hapus balasan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                        <p class="mt-1 max-w-[60ch] whitespace-pre-line text-sm leading-relaxed">{{ $b->komentar }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-4xl bg-white p-8 text-center">
                <p class="text-sm font-semibold">Belum ada balasan</p>
                <p class="mx-auto mt-1.5 max-w-[40ch] text-sm leading-relaxed text-muted">
                    Kalau kamu tahu jawabannya, tulis satu penjelasan singkat.
                </p>
            </div>
        @endforelse
    </div>
@endsection

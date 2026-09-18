@php
    /** @var \App\Models\Post $post */
    $penulis   = $post->user->profil->nama_lengkap ?? 'Pengguna Eduvia';
    $institusi = $post->user->profil->institusi ?? null;
    $avatar    = ($post->user->profil->photo ?? null) ? asset('storage/' . $post->user->profil->photo) : null;
    $jmlLike   = $post->likes_count ?? $post->likes()->count();
    $jmlKomen  = $post->komentar_count ?? $post->komentar()->count();
    $sudahLike = $post->disukai;
    $waktu     = $post->dibuat_pada ? $post->dibuat_pada->diffForHumans() : 'baru saja';
    $milikSaya = $post->milikSaya();
@endphp

<article class="rounded-4xl bg-white p-6">
    <div class="flex items-start gap-3">
        <a href="{{ route('user.show', $post->id_user) }}" class="shrink-0">
            @if ($avatar)
                <img src="{{ $avatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
            @else
                <span class="grid h-11 w-11 place-items-center rounded-full bg-limesoft text-sm font-bold">
                    {{ strtoupper(mb_substr($penulis, 0, 1)) }}
                </span>
            @endif
        </a>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <a href="{{ route('user.show', $post->id_user) }}" class="text-sm font-bold hover:underline">{{ $penulis }}</a>
                @if ($institusi)
                    <span class="text-xs text-muted">{{ $institusi }}</span>
                @endif
                <span class="text-xs text-muted">&middot; {{ $waktu }}</span>
                @if ($post->diperbarui_pada)
                    <span class="text-xs text-muted">&middot; diedit</span>
                @endif
            </div>

            @if ($post->komunitas)
                <a href="{{ route('komunitas.show', $post->id_komunitas) }}"
                   class="mt-1.5 inline-flex rounded-full bg-canvas px-3 py-1 text-xs font-semibold text-forest hover:bg-limesoft">
                    {{ $post->komunitas->nama_komunitas }}
                </a>
            @endif

            <p class="mt-3 max-w-[62ch] whitespace-pre-line text-[15px] leading-relaxed">{{ $post->konten }}</p>

            @if ($post->gambar)
                <img src="{{ asset('storage/' . $post->gambar) }}" alt="" class="mt-4 w-full rounded-3xl object-cover">
            @endif

            @if ($post->file)
                <a href="{{ asset('storage/' . $post->file) }}" target="_blank" rel="noopener"
                   class="mt-4 inline-flex items-center gap-2 rounded-2xl border border-line px-4 py-3 text-sm font-semibold hover:border-forest">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/>
                    </svg>
                    {{ basename($post->file) }}
                </a>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <form action="{{ route('post.like', $post->id_post) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold transition
                                   {{ $sudahLike ? 'bg-tangerine/15 text-ember' : 'text-muted hover:bg-canvas hover:text-ink' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $sudahLike ? 'currentColor' : 'none' }}"
                             stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path d="M12 20s-7-4.4-7-9.2A4 4 0 0 1 12 8a4 4 0 0 1 7 2.8C19 15.6 12 20 12 20Z"/>
                        </svg>
                        {{ $jmlLike }}
                    </button>
                </form>

                <a href="{{ route('post.show', $post->id_post) }}"
                   class="flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold text-muted hover:bg-canvas hover:text-ink">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path d="M20 12a7 7 0 0 1-7 7H8l-4 2 1.2-3.4A7 7 0 0 1 11 5h2a7 7 0 0 1 7 7Z"/>
                    </svg>
                    {{ $jmlKomen }}
                </a>

                <button type="button"
                        class="rounded-full px-3 py-2 text-sm font-semibold text-muted hover:bg-canvas hover:text-ink"
                        onclick="navigator.clipboard?.writeText('{{ route('post.show', $post->id_post) }}'); this.textContent='Tautan disalin';">
                    Salin tautan
                </button>

                <div class="ml-auto flex items-center gap-1">
                    @if ($milikSaya)
                        <a href="{{ route('post.edit', $post->id_post) }}"
                           class="rounded-full px-3 py-2 text-xs font-semibold text-muted hover:bg-canvas hover:text-ink">Edit</a>
                        <form action="{{ route('post.destroy', $post->id_post) }}" method="POST"
                              onsubmit="return confirm('Hapus postingan ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-full px-3 py-2 text-xs font-semibold text-muted hover:bg-canvas hover:text-ember">Hapus</button>
                        </form>
                    @else
                        <a href="{{ route('report.create', ['tipe' => 'post', 'id' => $post->id_post, 'kembali' => url()->current()]) }}"
                           class="rounded-full px-3 py-2 text-xs font-semibold text-muted hover:bg-canvas hover:text-ember">Laporkan</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</article>

@extends('layouts.admin')

@section('title', 'Moderasi — Admin')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Moderasi konten</h1>
        <p class="mt-3 text-[15px] text-muted">Pemilik konten menerima notifikasi saat kontennya dihapus.</p>
    </header>

    <div class="grid gap-4 lg:grid-cols-2">

        <section>
            <h2 class="mb-3 text-sm font-bold">Postingan terbaru</h2>
            <div class="space-y-3">
                @forelse ($posts as $p)
                    <article class="rounded-4xl bg-white p-5">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span class="font-semibold text-ink">{{ $p->user->profil->nama_lengkap ?? '-' }}</span>
                            <span>&middot; {{ $p->komunitas->nama_komunitas ?? '-' }}</span>
                            <span>&middot; {{ optional($p->dibuat_pada)->diffForHumans() }}</span>
                            <span class="ml-auto">{{ $p->likes_count }} suka &middot; {{ $p->komentar_count }} komentar</span>
                        </div>
                        <p class="mt-3 max-w-[64ch] text-sm leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($p->konten, 220) }}
                        </p>
                        <div class="mt-4 flex gap-2 border-t border-line pt-4">
                            <a href="{{ route('post.show', $p->id_post) }}" target="_blank" rel="noopener"
                               class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">Buka</a>
                            <form action="{{ route('admin.moderasi.post', $p->id_post) }}" method="POST"
                                  onsubmit="return confirm('Hapus postingan ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-4xl bg-white p-8 text-center text-sm text-muted">Belum ada postingan.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $posts->links() }}</div>
        </section>

        <section>
            <h2 class="mb-3 text-sm font-bold">Komentar terbaru</h2>
            <div class="space-y-3">
                @forelse ($komentar as $k)
                    <article class="rounded-4xl bg-white p-5">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span class="font-semibold text-ink">{{ $k->user->profil->nama_lengkap ?? '-' }}</span>
                            <span>&middot; {{ optional($k->dibuat_pada)->diffForHumans() }}</span>
                        </div>
                        <p class="mt-3 max-w-[64ch] text-sm leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($k->komentar, 220) }}
                        </p>
                        <div class="mt-4 flex gap-2 border-t border-line pt-4">
                            @if ($k->post)
                                <a href="{{ route('post.show', $k->id_post) }}" target="_blank" rel="noopener"
                                   class="rounded-full bg-canvas px-4 py-2 text-xs font-semibold hover:bg-line">Lihat diskusi</a>
                            @endif
                            <form action="{{ route('admin.moderasi.komentar', $k->id_komen) }}" method="POST"
                                  onsubmit="return confirm('Hapus komentar ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-full px-4 py-2 text-xs font-semibold text-muted hover:text-ember">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-4xl bg-white p-8 text-center text-sm text-muted">Belum ada komentar.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $komentar->links() }}</div>
        </section>
    </div>
@endsection

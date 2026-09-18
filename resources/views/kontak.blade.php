@extends(auth()->check() ? 'layouts.user' : 'layouts.publik')

@section('title', 'Kontak — Eduvia')

@section('content')

    <section class="rounded-4xl bg-white p-7 md:p-10">
        <h1 class="headline max-w-[16ch] text-[38px] font-extrabold md:text-[50px]">Butuh bantuan?</h1>
        <p class="mt-5 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Kalau menemukan kendala teknis, ingin melaporkan konten, atau punya masukan untuk Eduvia,
            hubungi tim kami lewat kanal berikut.
        </p>

        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl bg-canvas p-6">
                <dt class="text-xs font-semibold text-muted">Email</dt>
                <dd class="mt-1 text-base font-bold">halo@ruangbelajar.test</dd>
            </div>
            <div class="rounded-3xl bg-canvas p-6">
                <dt class="text-xs font-semibold text-muted">Jam layanan</dt>
                <dd class="mt-1 text-base font-bold">Senin&ndash;Jumat, 09.00&ndash;17.00 WIB</dd>
            </div>
            <div class="rounded-3xl bg-canvas p-6">
                <dt class="text-xs font-semibold text-muted">Laporan konten</dt>
                <dd class="mt-1 text-base font-bold">
                    @auth
                        <a href="{{ route('report.index') }}" class="text-forest hover:underline">Lewat menu Laporan saya</a>
                    @else
                        Tersedia setelah login
                    @endauth
                </dd>
            </div>
            <div class="rounded-3xl bg-canvas p-6">
                <dt class="text-xs font-semibold text-muted">Pengajuan komunitas</dt>
                <dd class="mt-1 text-base font-bold">
                    @auth
                        <a href="{{ route('pengajuan.create') }}" class="text-forest hover:underline">Menu Pengajuan</a>
                    @else
                        Tersedia setelah login
                    @endauth
                </dd>
            </div>
        </dl>
    </section>
@endsection

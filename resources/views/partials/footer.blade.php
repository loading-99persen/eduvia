{{-- Footer global Eduvia --}}
<footer class="mt-10 border-t border-line">
    <div class="mx-auto max-w-[1400px] px-4 py-10 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Brand --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2.5">
                    @include('partials.logo', ['teks' => false, 'ukuran' => 'h-11 w-auto'])
                </div>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-muted">
                    Satu platform, seribu inspirasi. Tempat belajar bareng komunitas, ikut diskusi,
                    dan hadir di webinar dari para leader komunitas.
                </p>
            </div>

            {{-- Jelajahi --}}
            <div>
                <p class="text-sm font-bold text-ink">Jelajahi</p>
                <ul class="mt-3 space-y-2 text-sm text-muted">
                    <li><a href="{{ route('beranda') }}" class="hover:text-forest">Beranda</a></li>
                    <li><a href="{{ route('komunitas.index') }}" class="hover:text-forest">Komunitas</a></li>
                    <li><a href="{{ route('webinar.index') }}" class="hover:text-forest">Webinar</a></li>
                    <li><a href="{{ route('user.jadwal') }}" class="hover:text-forest">Jadwal saya</a></li>
                </ul>
            </div>

            {{-- Lainnya --}}
            <div>
                <p class="text-sm font-bold text-ink">Lainnya</p>
                <ul class="mt-3 space-y-2 text-sm text-muted">
                    <li><a href="{{ route('tentang') }}" class="hover:text-forest">Tentang Eduvia</a></li>
                    <li><a href="{{ route('kontak') }}" class="hover:text-forest">Kontak</a></li>
                    <li><a href="{{ route('pengajuan.index') }}" class="hover:text-forest">Ajukan komunitas</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 border-t border-line pt-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
            <span>&copy; {{ date('Y') }} Eduvia. Seluruh hak cipta dilindungi.</span>
            <span>Dibuat untuk kebutuhan lomba.</span>
        </div>
    </div>
</footer>

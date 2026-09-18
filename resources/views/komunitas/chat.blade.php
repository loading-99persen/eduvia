@extends('layouts.user')

@section('title', 'Group chat ' . $komunitas->nama_komunitas . ' — Eduvia')

@section('content')
    <div class="flex h-[calc(100vh-13rem)] flex-col overflow-hidden rounded-4xl bg-white">

        <div class="flex items-center gap-3 border-b border-line px-6 py-4">
            <a href="{{ route('komunitas.show', $komunitas->id_komunitas) }}"
               class="grid h-10 w-10 place-items-center rounded-full bg-canvas hover:bg-line" aria-label="Kembali ke komunitas">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 5 8 12l7 7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-base font-extrabold tracking-tight">{{ $komunitas->nama_komunitas }}</h1>
                <p class="text-xs text-muted">Group chat komunitas</p>
            </div>
            <span class="ml-auto rounded-full bg-limesoft px-3 py-1 text-xs font-semibold text-forest">Hanya anggota</span>
        </div>

        <div id="daftar-pesan" class="flex-1 space-y-4 overflow-y-auto bg-canvas px-6 py-6">
            @forelse ($pesan as $p)
                @php $saya = (int) $p->id_user === (int) auth()->id(); @endphp
                <div class="flex {{ $saya ? 'justify-end' : 'justify-start' }}" data-id="{{ $p->id_pesan }}">
                    <div class="max-w-[74%]">
                        @unless ($saya)
                            <p class="mb-1 px-1 text-xs font-semibold text-muted">{{ $p->user->profil->nama_lengkap ?? 'Anggota' }}</p>
                        @endunless
                        <div class="rounded-3xl px-4 py-3 text-sm leading-relaxed {{ $saya ? 'rounded-br-lg bg-forest text-white' : 'rounded-bl-lg bg-white' }}">
                            {{ $p->pesan }}
                        </div>
                        <p class="mt-1 px-1 text-[11px] text-muted {{ $saya ? 'text-right' : '' }}"
                           data-utc="{{ optional($p->dikirim_pada)->utc()->format('Y-m-d H:i') }}">
                            {{ optional($p->dikirim_pada)->format('H:i') }}
                        </p>
                    </div>
                </div>
            @empty
                <p id="chat-kosong" class="py-10 text-center text-sm text-muted">
                    Belum ada pesan. Sapa anggota lain lebih dulu.
                </p>
            @endforelse
        </div>

        <form id="form-pesan" action="{{ route('komunitas.chat.kirim', $komunitas->id_komunitas) }}" method="POST"
              class="flex items-center gap-3 border-t border-line px-4 py-4 sm:px-6">
            @csrf
            <label class="sr-only" for="pesan">Tulis pesan</label>
            <input id="pesan" name="pesan" autocomplete="off" required maxlength="2000" placeholder="Tulis pesan…"
                   class="flex-1 rounded-full border-0 bg-canvas px-5 py-3 text-sm placeholder:text-muted focus:ring-2 focus:ring-forest">
            <button class="rounded-full bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forestdim">Kirim</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const kotak   = document.getElementById('daftar-pesan');
    const form    = document.getElementById('form-pesan');
    const input   = document.getElementById('pesan');
    const token   = document.querySelector('meta[name="csrf-token"]').content;
    const urlJson = @json(route('komunitas.chat.json', $komunitas->id_komunitas));
    const urlKirim = @json(route('komunitas.chat.kirim', $komunitas->id_komunitas));

    let terakhir = {{ $pesan->max('id_pesan') ?? 0 }};

    const keBawah = () => { kotak.scrollTop = kotak.scrollHeight; };
    keBawah();

    function gambarPesan(p) {
        const kosong = document.getElementById('chat-kosong');
        if (kosong) kosong.remove();

        const baris = document.createElement('div');
        baris.className = 'flex ' + (p.saya ? 'justify-end' : 'justify-start');
        baris.dataset.id = p.id_pesan;

        const bungkus = document.createElement('div');
        bungkus.className = 'max-w-[74%]';

        if (!p.saya) {
            const nama = document.createElement('p');
            nama.className = 'mb-1 px-1 text-xs font-semibold text-muted';
            nama.textContent = p.nama;
            bungkus.appendChild(nama);
        }

        const isi = document.createElement('div');
        isi.className = 'rounded-3xl px-4 py-3 text-sm leading-relaxed ' +
            (p.saya ? 'rounded-br-lg bg-forest text-white' : 'rounded-bl-lg bg-white');
        isi.textContent = p.pesan;
        bungkus.appendChild(isi);

        const jam = document.createElement('p');
        jam.className = 'mt-1 px-1 text-[11px] text-muted ' + (p.saya ? 'text-right' : '');
        jam.textContent = p.jam || '';
        if (p.utc) jam.setAttribute('data-utc', p.utc);
        bungkus.appendChild(jam);

        baris.appendChild(bungkus);
        kotak.appendChild(baris);

        if (window.eduviaSesuaikanJam) window.eduviaSesuaikanJam(baris);
    }

    async function ambilBaru() {
        try {
            const res = await fetch(urlJson + '?sejak=' + terakhir, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) return;

            const data = await res.json();

            if (data.pesan && data.pesan.length) {
                data.pesan.forEach(p => {
                    if (p.id_pesan > terakhir) {
                        gambarPesan(p);
                        terakhir = p.id_pesan;
                    }
                });
                keBawah();
            }
        } catch (e) {
            /* diamkan: koneksi sementara terganggu */
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const teks = input.value.trim();
        if (!teks) return;

        input.value = '';

        try {
            await fetch(urlKirim, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ pesan: teks })
            });

            await ambilBaru();
        } catch (err) {
            // Bila JavaScript gagal, kirim lewat cara biasa.
            input.value = teks;
            form.submit();
        }
    });

    setInterval(ambilBaru, 4000);
})();
</script>
@endpush

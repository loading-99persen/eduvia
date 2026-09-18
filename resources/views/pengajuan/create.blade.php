@extends('layouts.user')

@section('title', 'Buat pengajuan — Eduvia')

@section('content')

    <header class="mb-6">
        <h1 class="headline text-[38px] font-extrabold md:text-[46px]">Buat pengajuan</h1>
        <p class="mt-3 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Isi selengkap mungkin. Semakin jelas tujuannya, semakin cepat admin bisa meninjau.
        </p>
    </header>

    <div class="mb-5 flex gap-1 rounded-full bg-white p-1">
        @foreach (['komunitas' => 'Komunitas baru', 'webinar' => 'Webinar'] as $key => $label)
            <a href="{{ route('pengajuan.create', ['tipe' => $key]) }}"
               class="flex-1 rounded-full px-4 py-2.5 text-center text-sm font-semibold transition
                      {{ $tipe === $key ? 'bg-forest text-white' : 'text-muted hover:text-ink' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($tipe === 'komunitas')

        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data"
              class="rounded-4xl bg-white p-7">
            @csrf
            <input type="hidden" name="tipe" value="komunitas">

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="nama_komunitas" class="mb-1.5 block text-sm font-semibold">Nama komunitas</label>
                    <input id="nama_komunitas" name="nama_komunitas" required value="{{ old('nama_komunitas') }}"
                           placeholder="Misalnya: Kalkulus Dasar"
                           class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                </div>

                <div>
                    <label for="kategori" class="mb-1.5 block text-sm font-semibold">Kategori</label>
                    <select id="kategori" name="kategori" required
                            class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategori as $kat)
                            <option value="{{ $kat }}" @selected(old('kategori') === $kat)>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="gambar" class="mb-1.5 block text-sm font-semibold">Logo atau banner (opsional)</label>
                    <input id="gambar" type="file" name="gambar" accept="image/*"
                           class="block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-canvas file:px-5 file:py-2.5 file:text-sm file:font-semibold hover:file:bg-line">
                </div>

                <div class="sm:col-span-2">
                    <label for="deskripsi" class="mb-1.5 block text-sm font-semibold">Deskripsi komunitas</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required
                              placeholder="Apa yang akan dibahas di komunitas ini, dan untuk siapa?"
                              class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="alasan" class="mb-1.5 block text-sm font-semibold">Alasan pengajuan</label>
                    <textarea id="alasan" name="alasan" rows="3" required
                              placeholder="Kenapa komunitas ini perlu ada, dan bagaimana kamu akan mengelolanya?"
                              class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('alasan') }}</textarea>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-canvas p-5 text-sm leading-relaxed text-muted">
                Jika disetujui, kamu otomatis menjadi leader komunitas ini dan group chat-nya dibuatkan.
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-line pt-6">
                <button class="rounded-full bg-forest px-7 py-3 text-sm font-semibold text-white hover:bg-forestdim">Kirim pengajuan</button>
                <a href="{{ route('pengajuan.index') }}" class="text-sm font-semibold text-muted hover:text-ink">Batal</a>
            </div>
        </form>

    @else

        @if ($komunitasDipimpin->isEmpty())
            <div class="rounded-4xl bg-white p-10 text-center">
                <p class="text-lg font-bold">Kamu belum memimpin komunitas</p>
                <p class="mx-auto mt-2 max-w-[48ch] text-sm leading-relaxed text-muted">
                    Webinar hanya bisa diajukan oleh leader untuk komunitasnya sendiri. Ajukan komunitas baru dulu,
                    dan setelah disetujui kamu bisa mengadakan webinar di sana.
                </p>
                <a href="{{ route('pengajuan.create', ['tipe' => 'komunitas']) }}"
                   class="mt-5 inline-flex rounded-full bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forestdim">
                    Ajukan komunitas
                </a>
            </div>
        @else
            <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data"
                  class="rounded-4xl bg-white p-7">
                @csrf
                <input type="hidden" name="tipe" value="webinar">

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="id_komunitas" class="mb-1.5 block text-sm font-semibold">Komunitas penyelenggara</label>
                        <select id="id_komunitas" name="id_komunitas" required
                                class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                            @foreach ($komunitasDipimpin as $k)
                                <option value="{{ $k->id_komunitas }}"
                                    @selected(old('id_komunitas', request('komunitas')) == $k->id_komunitas)>
                                    {{ $k->nama_komunitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="judul" class="mb-1.5 block text-sm font-semibold">Judul webinar</label>
                        <input id="judul" name="judul" required value="{{ old('judul') }}"
                               class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                    </div>

                    <div>
                        <label for="pembicara" class="mb-1.5 block text-sm font-semibold">Pembicara</label>
                        <input id="pembicara" name="pembicara" required
                               value="{{ old('pembicara', auth()->user()->nama) }}"
                               class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                    </div>

                    <div>
                        <label for="kategori" class="mb-1.5 block text-sm font-semibold">Kategori</label>
                        <select id="kategori" name="kategori" required
                                class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                            <option value="">Pilih kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat }}" @selected(old('kategori') === $kat)>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tanggal" class="mb-1.5 block text-sm font-semibold">Tanggal</label>
                        <input id="tanggal" type="date" name="tanggal" required
                               min="{{ now()->toDateString() }}" value="{{ old('tanggal') }}"
                               class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                    </div>

                    <div>
                        <label for="waktu" class="mb-1.5 block text-sm font-semibold">Waktu mulai (WIB)</label>
                        <input id="waktu" type="time" name="waktu" required value="{{ old('waktu') }}"
                               class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                    </div>

                    <div>
                        <label for="link_meeting" class="mb-1.5 block text-sm font-semibold">Tautan meeting (opsional)</label>
                        <input id="link_meeting" type="url" name="link_meeting" value="{{ old('link_meeting') }}"
                               placeholder="https://meet.google.com/…"
                               class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
                        <p class="mt-1.5 text-xs text-muted">Bisa diisi belakangan dari halaman webinar.</p>
                    </div>

                    <div>
                        <label for="foto" class="mb-1.5 block text-sm font-semibold">Poster (opsional)</label>
                        <input id="foto" type="file" name="foto" accept="image/*"
                               class="block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-canvas file:px-5 file:py-2.5 file:text-sm file:font-semibold hover:file:bg-line">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="deskripsi" class="mb-1.5 block text-sm font-semibold">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required
                                  placeholder="Apa yang akan dibahas, untuk siapa, dan apa yang perlu disiapkan peserta?"
                                  class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-forest">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl bg-canvas p-5 text-sm leading-relaxed text-muted">
                    Tautan meeting hanya terbuka untuk peserta terdaftar, mulai
                    {{ \App\Models\Webinar::MENIT_AKSES_AWAL }} menit sebelum waktu mulai.
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-line pt-6">
                    <button class="rounded-full bg-forest px-7 py-3 text-sm font-semibold text-white hover:bg-forestdim">Kirim pengajuan</button>
                    <a href="{{ route('pengajuan.index') }}" class="text-sm font-semibold text-muted hover:text-ink">Batal</a>
                </div>
            </form>
        @endif
    @endif
@endsection

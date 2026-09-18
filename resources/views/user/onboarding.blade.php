@extends('layouts.user')

@section('title', 'Minat belajar — Eduvia')

@php
    $terpilih = old('minat', $minatTerpilih);
    $prefAktif = old('preferensi', $preferensi);
@endphp

@section('content')

    <section class="mb-6 overflow-hidden rounded-4xl bg-white p-7 md:p-9">
        <h1 class="headline max-w-[18ch] text-[38px] font-extrabold md:text-[46px]">
            Apa yang ingin kamu pelajari?
        </h1>
        <p class="mt-4 max-w-[56ch] text-[15px] leading-relaxed text-muted">
            Pilih topik yang kamu suka. Beranda, komunitas, dan webinar yang muncul akan mengikuti pilihanmu,
            dan kamu bisa mengubahnya kapan saja.
        </p>
    </section>

    <form action="{{ route('user.onboarding.store') }}" method="POST">
        @csrf

        <section class="mb-4 rounded-4xl bg-white p-7">
            <h2 class="text-sm font-bold">Topik yang kamu suka</h2>
            <p class="mt-1 text-sm text-muted">Pilih minimal satu, boleh lebih dari satu.</p>

            <div class="mt-5 flex flex-wrap gap-2.5">
                @foreach ($semuaMinat as $m)
                    @php $aktif = in_array($m->id_minat, (array) $terpilih); @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox" name="minat[]" value="{{ $m->id_minat }}" class="peer sr-only" @checked($aktif)>
                        <span class="inline-flex items-center gap-2 rounded-full bg-canvas px-5 py-3 text-sm font-semibold text-muted transition
                                     peer-checked:bg-forest peer-checked:text-white hover:text-ink">
                            {{ $m->nama_minat }}
                        </span>
                    </label>
                @endforeach
            </div>

            @error('minat')<p class="mt-3 text-xs text-ember">{{ $message }}</p>@enderror
        </section>

        <section class="mb-4 rounded-4xl bg-white p-7">
            <h2 class="text-sm font-bold">Cara belajar yang paling cocok untukmu</h2>
            <p class="mt-1 text-sm text-muted">Dipakai untuk menentukan jenis konten yang lebih sering muncul.</p>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                @foreach ([
                    'diskusi' => ['Diskusi dan tanya jawab', 'Belajar dari pertanyaan orang lain.'],
                    'webinar' => ['Webinar dan materi langsung', 'Lebih suka mendengar penjelasan.'],
                    'praktik' => ['Latihan dan studi kasus', 'Belajar sambil mengerjakan.'],
                ] as $nilai => $isi)
                    <label class="cursor-pointer">
                        <input type="radio" name="preferensi" value="{{ $nilai }}" class="peer sr-only" @checked($prefAktif === $nilai)>
                        <span class="block h-full rounded-3xl border-2 border-line p-5 transition peer-checked:border-forest peer-checked:bg-limesoft/40">
                            <span class="block text-sm font-bold">{{ $isi[0] }}</span>
                            <span class="mt-1.5 block text-sm leading-relaxed text-muted">{{ $isi[1] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button class="rounded-full bg-forest px-7 py-3.5 text-sm font-semibold text-white hover:bg-forestdim">
                Simpan dan lanjutkan
            </button>
            @if (auth()->user()->onboardingSelesai())
                <a href="{{ route('beranda') }}" class="text-sm font-semibold text-muted hover:text-ink">Batal</a>
            @endif
        </div>
    </form>
@endsection

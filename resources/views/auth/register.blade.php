@extends('layouts.auth')

@section('title', 'Daftar — Eduvia')

@section('content')
    <h1 class="mt-6 text-2xl font-extrabold tracking-tight">Buat akun</h1>
    <p class="mt-1 text-sm text-muted">Gratis. Setelah daftar kamu akan diminta melengkapi profil dan memilih minat belajar.</p>

    @include('partials.flash')

    <form action="{{ route('register.proses') }}" method="POST" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="nama_lengkap" class="mb-1.5 block text-sm font-semibold">Nama lengkap</label>
            <input id="nama_lengkap" name="nama_lengkap" required autofocus value="{{ old('nama_lengkap') }}"
                   class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            @error('nama_lengkap')<p class="mt-1.5 text-xs text-ember">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" required value="{{ old('email') }}"
                   class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            @error('email')<p class="mt-1.5 text-xs text-ember">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold">Kata sandi</label>
            <input id="password" type="password" name="password" required minlength="6"
                   class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            <p class="mt-1.5 text-xs text-muted">Minimal 6 karakter.</p>
            @error('password')<p class="mt-1.5 text-xs text-ember">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold">Ulangi kata sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
        </div>

        <button class="w-full rounded-full bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forestdim">Daftar</button>
    </form>

    <div class="my-5 flex items-center gap-3">
        <span class="h-px flex-1 bg-line"></span>
        <span class="text-xs text-muted">atau</span>
        <span class="h-px flex-1 bg-line"></span>
    </div>

    <a href="{{ route('google.login') }}"
       class="flex items-center justify-center gap-2 rounded-full border border-line px-5 py-3 text-sm font-semibold hover:bg-canvas">
        @include('partials.ikon-google')
        Daftar dengan Google
    </a>

    <p class="mt-6 text-center text-sm text-muted">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-forest hover:underline">Masuk</a>
    </p>
@endsection

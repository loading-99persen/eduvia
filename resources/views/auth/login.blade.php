@extends('layouts.auth')

@section('title', 'Masuk — Eduvia')

@section('content')
    <h1 class="mt-6 text-2xl font-extrabold tracking-tight">Selamat datang kembali</h1>
    <p class="mt-1 text-sm text-muted">Masuk untuk lanjut belajar bersama komunitasmu.</p>

    @include('partials.flash')

    <form action="{{ route('login.proses') }}" method="POST" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" required autofocus value="{{ old('email') }}"
                   class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            @error('email')<p class="mt-1.5 text-xs text-ember">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold">Kata sandi</label>
            <input id="password" type="password" name="password" required
                   class="w-full rounded-2xl border-0 bg-canvas px-4 py-3 text-sm focus:ring-2 focus:ring-forest">
            @error('password')<p class="mt-1.5 text-xs text-ember">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="ingat" value="1" class="rounded border-line text-forest focus:ring-forest">
            Ingat saya di perangkat ini
        </label>

        <button class="w-full rounded-full bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forestdim">Masuk</button>
    </form>

    <div class="my-5 flex items-center gap-3">
        <span class="h-px flex-1 bg-line"></span>
        <span class="text-xs text-muted">atau</span>
        <span class="h-px flex-1 bg-line"></span>
    </div>

    <a href="{{ route('google.login') }}"
       class="flex items-center justify-center gap-2 rounded-full border border-line px-5 py-3 text-sm font-semibold hover:bg-canvas">
        @include('partials.ikon-google')
        Masuk dengan Google
    </a>

    <p class="mt-6 text-center text-sm text-muted">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-forest hover:underline">Daftar</a>
    </p>
@endsection

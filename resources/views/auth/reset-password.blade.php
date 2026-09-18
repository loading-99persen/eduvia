@extends('layouts.auth')

@section('title', 'Atur ulang kata sandi — Eduvia')

@section('form')

    <h1>Kata sandi baru</h1>

    <p class="subtitle">
        Buat kata sandi baru untuk akun {{ $email }}.
    </p>

    <form action="{{ route('password.update') }}" method="POST">

        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <label for="password">Kata sandi baru</label>

        <input
            id="password"
            type="password"
            name="password"
            placeholder="Minimal 8 karakter"
            required
            autofocus>

        @error('password')
            <span class="field-error">{{ $message }}</span>
        @enderror

        <label for="password_confirmation">Ulangi kata sandi</label>

        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            placeholder="Ketik ulang kata sandi"
            required>

        <button class="submit-btn" type="submit">Simpan kata sandi</button>

    </form>

    <div class="bottom">
        Batal?
        <a href="{{ route('login') }}">Kembali ke login</a>
    </div>

@endsection

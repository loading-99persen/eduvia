@extends('layouts.auth')

@section('title', 'Lupa kata sandi — Eduvia')

@section('form')

    <h1>Lupa kata sandi</h1>

    <p class="subtitle">
        Masukkan email akunmu, lalu ikuti tautan pemulihan yang kami buatkan.
    </p>

    <form action="{{ route('password.email') }}" method="POST">

        @csrf

        <label for="email">Email</label>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Email yang kamu daftarkan"
            required
            autofocus>

        @error('email')
            <span class="field-error">{{ $message }}</span>
        @enderror

        <button class="submit-btn" type="submit">Kirim tautan pemulihan</button>

    </form>

    <div class="bottom">
        Ingat kata sandimu?
        <a href="{{ route('login') }}">Masuk</a>
    </div>

@endsection

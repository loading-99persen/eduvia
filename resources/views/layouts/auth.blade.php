<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Eduvia')</title>
    @include('partials.tema')
</head>
<body class="flex min-h-screen items-center justify-center bg-canvas p-4 font-sans text-ink">

    <div class="w-full max-w-sm rounded-4xl bg-white p-8">
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
            @include('partials.logo', ['teks' => false, 'ukuran' => 'h-10 w-auto'])
        </a>

        @yield('content')
    </div>
</body>
</html>

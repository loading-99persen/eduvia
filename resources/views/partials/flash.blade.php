@if (session('success'))
    <p class="mt-4 rounded-2xl bg-forest px-4 py-3 text-sm text-white">{{ session('success') }}</p>
@endif

@if (session('error'))
    <p class="mt-4 rounded-2xl border border-tangerine bg-tangerine/10 px-4 py-3 text-sm">{{ session('error') }}</p>
@endif

@if ($errors->any())
    <ul class="mt-4 space-y-1 rounded-2xl border border-tangerine bg-tangerine/10 px-4 py-3 text-sm">
        @foreach ($errors->all() as $pesan)
            <li>{{ $pesan }}</li>
        @endforeach
    </ul>
@endif

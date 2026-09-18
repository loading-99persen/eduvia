{{-- Logo Eduvia. $teks = tampilkan nama, $badge = teks kecil di samping, $ukuran = kelas tinggi ikon --}}
@php
    $teks   = $teks   ?? true;
    $ukuran = $ukuran ?? 'h-9 w-auto';
    $badge  = $badge  ?? null;
@endphp
<img src="{{ asset('images/logo-eduvia-mark.png') }}" alt="Eduvia" class="{{ $ukuran }} shrink-0 object-contain">
@if ($teks)
    <span class="text-[17px] font-extrabold tracking-tight text-forest">Eduvia</span>
@endif
@if ($badge)
    <span class="rounded-full bg-forest px-3 py-1 text-xs font-bold text-lime">{{ $badge }}</span>
@endif

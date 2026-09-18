{{-- Tema global Eduvia: dipakai semua layout agar palet konsisten --}}
<link rel="icon" href="{{ asset('images/logo-eduvia-mark.png') }}">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

<script src="https://cdn.tailwindcss.com"></script>
<script>
    /* Palet warna Eduvia */
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    canvas:    '#FAF8F5', /* background utama  */
                    ink:       '#252128', /* teks              */
                    forest:    '#35266F', /* primary deep plum */
                    forestdim: '#4A3894',
                    lime:      '#62CFB6', /* secondary mint    */
                    limesoft:  '#E2F6F0',
                    sun:       '#FFE8D2',
                    tangerine: '#FFB576', /* accent apricot    */
                    ember:     '#B4550F', /* apricot gelap utk teks */
                    lilac:     '#F7A8C4', /* highlight blush   */
                    muted:     '#6E6880',
                    line:      '#ECE6E0',
                },
                fontFamily: { sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'] },
                borderRadius: { '4xl': '2rem' },
            }
        }
    }
</script>

<style>
    body { -webkit-font-smoothing: antialiased; background-image:
        radial-gradient(60rem 30rem at 110% -10%, #E2F6F0 0%, transparent 60%),
        radial-gradient(50rem 26rem at -10% 0%, #FDEDF3 0%, transparent 60%); background-attachment: fixed; }
    .headline { letter-spacing: -0.035em; line-height: 0.95; }

    /* Kartu putih diberi garis + bayangan halus supaya tetap terbaca di atas canvas terang */
    .bg-white.rounded-4xl, .bg-white.rounded-3xl, .bg-white.rounded-2xl, .bg-white.rounded-full {
        border: 1px solid #EFE9E2;
        box-shadow: 0 1px 2px rgba(37,33,40,.04), 0 8px 24px -16px rgba(53,38,111,.18);
    }
    /* Field & chip: canvas terlalu dekat dengan putih, beri nada plum tipis */
    input.bg-canvas, textarea.bg-canvas, select.bg-canvas {
        background-color: #F4F1F8; box-shadow: inset 0 0 0 1px #E6E0EE;
    }
    .bg-canvas.rounded-full, .bg-canvas.rounded-xl, .bg-canvas.rounded-2xl, .bg-canvas.rounded-3xl { background-color: #F4F1F8; }

    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-thumb { background: #DCD5E6; border-radius: 99px; border: 3px solid #FAF8F5; }
    [x-cloak] { display: none; }
    @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }
    a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible, select:focus-visible {
        outline: 2px solid #35266F; outline-offset: 2px; border-radius: 10px;
    }
</style>

<script>
    /* Jam Eduvia, otomatis ditampilkan sesuai waktu device (laptop/HP) pengguna.
       - [data-wib]  = waktu yang diinput admin sebagai jam WIB polos (mis. jadwal webinar).
       - [data-utc]  = waktu asli server (UTC), mis. waktu kirim chat.
       Format tampil diatur lewat [data-wib-fmt]/[data-utc-fmt]: "jam" | "tanggal" | "tanggal-jam". */
    function eduviaFormatJam(tgl, fmt) {
        if (fmt === 'jam') {
            return tgl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        } else if (fmt === 'tanggal') {
            return tgl.toLocaleDateString('id-ID', { day: 'numeric' });
        }
        return tgl.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
            + ' \u00b7 ' + tgl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
    function eduviaSesuaikanJam(akar = document) {
        akar.querySelectorAll('[data-wib]').forEach((el) => {
            const nilai = el.getAttribute('data-wib');
            if (!nilai) return;
            const tgl = new Date(nilai.replace(' ', 'T') + '+07:00'); // input WIB polos
            if (isNaN(tgl)) return;
            el.textContent = eduviaFormatJam(tgl, el.getAttribute('data-wib-fmt') || 'jam');
        });
        akar.querySelectorAll('[data-utc]').forEach((el) => {
            const nilai = el.getAttribute('data-utc');
            if (!nilai) return;
            const tgl = new Date(nilai.replace(' ', 'T') + 'Z'); // catatan waktu server (UTC asli)
            if (isNaN(tgl)) return;
            el.textContent = eduviaFormatJam(tgl, el.getAttribute('data-utc-fmt') || 'jam');
        });
    }
    document.addEventListener('DOMContentLoaded', () => eduviaSesuaikanJam());
</script>

# Catatan Penggabungan Eduvia

Project ini adalah gabungan dari dua versi yang kamu kirim.

## Sumber tiap bagian

| Bagian | Diambil dari |
| --- | --- |
| Homepage publik (`/`, Tentang, Kontak) | Eduvia.zip |
| Seluruh halaman user (beranda, jelajahi, komunitas, group chat, postingan, webinar, jadwal, pengajuan, notifikasi, profil, onboarding) | Eduvia.zip — **tidak diubah** |
| Login, Register, Lupa & Reset kata sandi | Eduvia (4).zip (desain kartu kaca) |
| Halaman admin (sidebar ungu + navbar kartu) | Eduvia (4).zip |
| Controller, model, migration, middleware | Eduvia.zip |

## Penyesuaian yang dilakukan

**Auth**
- Desain login/register dari versi (4) dipindah ke `layouts/auth.blade.php` supaya satu sumber gaya untuk empat halaman auth.
- Form register ditambah field **Nama lengkap** (validasi di `AuthController` versi utama memerlukannya, versi (4) belum punya).
- Ditambahkan tampilan pesan error validasi dan flash message di kartu auth.
- Halaman **reset password** dibuat baru: di versi (4) route-nya sudah ada tapi file view-nya belum.
- Alur reset password diperbaiki: token diverifikasi dengan hash dan kedaluwarsa 60 menit. Migration `password_reset_tokens` ikut disalin.

**Admin**
- `layouts/admin.blade.php` ditulis ulang bergaya versi (4): sidebar `#35266f` (menu Dashboard, Users, Komunitas, Pengajuan, Webinar, Report, Moderasi) + navbar kartu putih, plus menu bawah untuk layar kecil.
- Judul tiap halaman admin dipindah ke navbar lewat `@section('judul')` dan `@section('subjudul')`.
- Ditambahkan halaman detail yang diminta ketentuan poin 21–23:
  - `admin/users-detail` — profil, minat belajar, komunitas yang diikuti beserta status leader/member, postingan terbaru, aksi aktif/nonaktif/ubah role/hapus.
  - `admin/komunitas-detail` — leader, daftar member beserta rolenya, jumlah diskusi, daftar webinar.
  - `admin/webinar-detail` — data webinar, leader pembuat, link meeting, dan daftar peserta yang diambil otomatis dari akun + profil user.
  - `admin/laporan-detail` — isi laporan, konten yang dilaporkan, dan form tindak lanjut.

**Perbaikan bug yang ditemukan saat digabung**
- `Report::target()` dipanggil di `admin/laporan.blade.php` tapi method-nya belum ada di model → ditambahkan (beserta accessor `label_status`).
- Folder `app/Http/Controllers/admin` diganti jadi `Admin`, dan `OnBoardingController.php` jadi `OnboardingController.php`, supaya autoload tetap jalan di server Linux yang membedakan huruf besar/kecil.

**Homepage**
- Gambar yang kamu kirim disimpan di `public/images/belajar-bersama.png` dan dipasang di hero homepage, di atas kartu "Yang bisa kamu lakukan di sini".

## Cara menjalankan

```bash
composer install          # kalau folder vendor perlu disegarkan
cp .env.example .env      # kalau .env belum sesuai, lalu isi koneksi database
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Akun bawaan dari seeder:

- Admin — `admin@ruangbelajar.com` / `admin123`
- User — `user1@ruangbelajar.com` / `user123`

## Catatan

Dokumen ketentuan memakai nama **RuangBelajar**, sedangkan kedua project memakai nama dan logo **Eduvia**. Fitur dan alurnya sudah disesuaikan ke ketentuan, tapi brandingnya dibiarkan Eduvia. Kalau mau diganti, yang perlu disentuh: `partials/logo.blade.php`, `partials/tema.blade.php`, teks di `landing`, `tentang`, `partials/footer`, file gambar di `public/images` dan `public/assets`, serta `APP_NAME` di `.env`.

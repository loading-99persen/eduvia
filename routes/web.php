<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KomunitasController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebinarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HALAMAN PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('beranda');
    }

    return view('landing');
})->name('landing');

Route::view('/tentang', 'tentang')->name('tentang');
Route::view('/kontak', 'kontak')->name('kontak');

/*
|--------------------------------------------------------------------------
| AUTENTIKASI
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'prosesLogin'])->name('login.proses');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'prosesRegister'])->name('register.proses');

    Route::get('/auth/google', [AuthController::class, 'redirectGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'callbackGoogle'])->name('google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| PROFIL & ONBOARDING (wajib login, boleh diakses sebelum profil lengkap)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('user.profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('user.profil.update');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('user.onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('user.onboarding.store');
});

/*
|--------------------------------------------------------------------------
| HALAMAN USER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'profil.lengkap'])->group(function () {

    // Beranda & dashboard
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('user.jadwal');
    Route::get('/pengguna/{id}', [ProfilController::class, 'show'])->name('user.show');

    /*
    | KOMUNITAS
    */
    Route::get('/komunitas', [KomunitasController::class, 'index'])->name('komunitas.index');
    Route::get('/komunitas-saya', [KomunitasController::class, 'saya'])->name('user.komunitas');
    Route::get('/komunitas/{id}', [KomunitasController::class, 'show'])
        ->whereNumber('id')->name('komunitas.show');
    Route::get('/komunitas/{id}/edit', [KomunitasController::class, 'edit'])
        ->whereNumber('id')->name('komunitas.edit');
    Route::put('/komunitas/{id}', [KomunitasController::class, 'update'])
        ->whereNumber('id')->name('komunitas.update');
    Route::post('/komunitas/{id}/gabung', [KomunitasController::class, 'gabung'])
        ->whereNumber('id')->name('komunitas.gabung');
    Route::delete('/komunitas/{id}/keluar', [KomunitasController::class, 'keluar'])
        ->whereNumber('id')->name('komunitas.keluar');

    /*
    | GROUP CHAT
    */
    Route::get('/komunitas/{id}/chat', [ChatController::class, 'show'])
        ->whereNumber('id')->name('komunitas.chat');
    Route::post('/komunitas/{id}/chat', [ChatController::class, 'kirim'])
        ->whereNumber('id')->name('komunitas.chat.kirim');
    Route::get('/komunitas/{id}/chat/json', [ChatController::class, 'json'])
        ->whereNumber('id')->name('komunitas.chat.json');

    /*
    | POSTINGAN
    */
    Route::post('/post', [PostController::class, 'store'])->name('post.store');
    Route::get('/post/{id}', [PostController::class, 'show'])->whereNumber('id')->name('post.show');
    Route::get('/post/{id}/edit', [PostController::class, 'edit'])->whereNumber('id')->name('post.edit');
    Route::put('/post/{id}', [PostController::class, 'update'])->whereNumber('id')->name('post.update');
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->whereNumber('id')->name('post.destroy');
    Route::post('/post/{id}/like', [PostController::class, 'like'])->whereNumber('id')->name('post.like');
    Route::post('/post/{id}/komentar', [PostController::class, 'komentar'])->whereNumber('id')->name('post.komentar');
    Route::delete('/komentar/{id}', [PostController::class, 'hapusKomentar'])->whereNumber('id')->name('komentar.destroy');

    /*
    | WEBINAR
    */
    Route::get('/webinar', [WebinarController::class, 'index'])->name('webinar.index');
    Route::get('/webinar/{id}', [WebinarController::class, 'show'])->whereNumber('id')->name('webinar.show');
    Route::post('/webinar/{id}/ikut', [WebinarController::class, 'ikut'])->whereNumber('id')->name('webinar.ikut');
    Route::delete('/webinar/{id}/batal', [WebinarController::class, 'batal'])->whereNumber('id')->name('webinar.batal');
    Route::put('/webinar/{id}/link', [WebinarController::class, 'updateLink'])->whereNumber('id')->name('webinar.link');

    /*
    | PENGAJUAN
    */
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->whereNumber('id')->name('pengajuan.destroy');

    /*
    | LAPORAN
    */
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/create', [ReportController::class, 'create'])->name('report.create');
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');
});

/*
|--------------------------------------------------------------------------
| NOTIFIKASI (tetap bisa diakses meski profil belum lengkap)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/buka', [NotifikasiController::class, 'buka'])->whereNumber('id')->name('notifikasi.buka');
    Route::put('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.bacaSemua');
    Route::put('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->whereNumber('id')->name('notifikasi.baca');
    Route::delete('/notifikasi/semua', [NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapusSemua');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->whereNumber('id')->name('notifikasi.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // User
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{id}', [AdminController::class, 'userUpdate'])->whereNumber('id')->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'userDestroy'])->whereNumber('id')->name('users.destroy');

    // Komunitas
    Route::get('/komunitas', [AdminController::class, 'komunitas'])->name('komunitas');
    Route::put('/komunitas/{id}', [AdminController::class, 'komunitasUpdate'])->whereNumber('id')->name('komunitas.update');
    Route::delete('/komunitas/{id}', [AdminController::class, 'komunitasDestroy'])->whereNumber('id')->name('komunitas.destroy');

    // Webinar
    Route::get('/webinar', [AdminController::class, 'webinar'])->name('webinar');
    Route::put('/webinar/{id}', [AdminController::class, 'webinarUpdate'])->whereNumber('id')->name('webinar.update');
    Route::delete('/webinar/{id}', [AdminController::class, 'webinarDestroy'])->whereNumber('id')->name('webinar.destroy');

    // Pengajuan
    Route::get('/pengajuan', [AdminController::class, 'pengajuan'])->name('pengajuan');
    Route::put('/pengajuan/{id}/terima', [AdminController::class, 'terima'])->whereNumber('id')->name('pengajuan.terima');
    Route::put('/pengajuan/{id}/tolak', [AdminController::class, 'tolak'])->whereNumber('id')->name('pengajuan.tolak');

    // Laporan & moderasi
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::put('/laporan/{id}', [AdminController::class, 'laporanUpdate'])->whereNumber('id')->name('laporan.update');
    Route::get('/moderasi', [AdminController::class, 'moderasi'])->name('moderasi');
    Route::delete('/moderasi/post/{id}', [AdminController::class, 'hapusPost'])->whereNumber('id')->name('moderasi.post');
    Route::delete('/moderasi/komentar/{id}', [AdminController::class, 'hapusKomentar'])->whereNumber('id')->name('moderasi.komentar');
});

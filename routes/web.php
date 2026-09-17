<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KomunitasController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotifikasiController;


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

// Login
Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'prosesLogin'])
    ->name('login.proses');

// Register
Route::get('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/register', [AuthController::class, 'prosesRegister'])
    ->name('register.proses');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [AuthController::class, 'redirectGoogle'])
    ->name('google.login');

Route::get('/auth/google/callback', [AuthController::class, 'callbackGoogle'])
    ->name('google.callback');


/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

// Beranda
Route::get('/beranda', function () {
    return view('beranda');
})->middleware('auth')->name('beranda');

// Admin
Route::get('/admin', function () {
    return view('admin.index');
})->middleware('auth')->name('admin');


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('/admin/users/create', [UserController::class, 'create'])
        ->name('users.create');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('users.store');

    Route::get('/admin/users/{id}', [UserController::class, 'show'])
        ->name('users.show');

    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])
        ->name('users.edit');

    Route::put('/admin/users/{id}', [UserController::class, 'update'])
        ->name('users.update');

    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])
        ->name('users.destroy');

});


/*
|--------------------------------------------------------------------------
| KOMUNITAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/komunitas', [KomunitasController::class, 'index'])
        ->name('komunitas.index');

    Route::get('/komunitas/create', [KomunitasController::class, 'create'])
        ->name('komunitas.create');

    Route::post('/komunitas', [KomunitasController::class, 'store'])
        ->name('komunitas.store');

    Route::get('/komunitas/{id}', [KomunitasController::class, 'show'])
        ->name('komunitas.show');

    Route::get('/komunitas/{id}/edit', [KomunitasController::class, 'edit'])
        ->name('komunitas.edit');

    Route::put('/komunitas/{id}', [KomunitasController::class, 'update'])
        ->name('komunitas.update');

    Route::delete('/komunitas/{id}', [KomunitasController::class, 'destroy'])
        ->name('komunitas.destroy');

    // Gabung komunitas
    Route::post('/komunitas/{id}/gabung', [KomunitasController::class, 'gabung'])
        ->name('komunitas.gabung');

    // Keluar komunitas
    Route::delete('/komunitas/{id}/keluar', [KomunitasController::class, 'keluar'])
        ->name('komunitas.keluar');

});


/*
|--------------------------------------------------------------------------
| POST
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/post', [PostController::class, 'index'])
        ->name('post.index');

    Route::get('/post/create', [PostController::class, 'create'])
        ->name('post.create');

    Route::post('/post', [PostController::class, 'store'])
        ->name('post.store');

    Route::get('/post/{id}', [PostController::class, 'show'])
        ->name('post.show');

    Route::get('/post/{id}/edit', [PostController::class, 'edit'])
        ->name('post.edit');

    Route::put('/post/{id}', [PostController::class, 'update'])
        ->name('post.update');

    Route::delete('/post/{id}', [PostController::class, 'destroy'])
        ->name('post.destroy');

    // Like
    Route::post('/post/{id}/like', [PostController::class, 'like'])
        ->name('post.like');

    // Komentar
    Route::post('/post/{id}/komentar', [PostController::class, 'komentar'])
        ->name('post.komentar');

});


/*
|--------------------------------------------------------------------------
| WEBINAR
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/webinar', [WebinarController::class, 'index'])
        ->name('webinar.index');

    Route::get('/webinar/create', [WebinarController::class, 'create'])
        ->name('webinar.create');

    Route::post('/webinar', [WebinarController::class, 'store'])
        ->name('webinar.store');

    Route::get('/webinar/{id}', [WebinarController::class, 'show'])
        ->name('webinar.show');

    Route::get('/webinar/{id}/edit', [WebinarController::class, 'edit'])
        ->name('webinar.edit');

    Route::put('/webinar/{id}', [WebinarController::class, 'update'])
        ->name('webinar.update');

    Route::delete('/webinar/{id}', [WebinarController::class, 'destroy'])
        ->name('webinar.destroy');

    // Ikut webinar
    Route::post('/webinar/{id}/ikut', [WebinarController::class, 'ikut'])
        ->name('webinar.ikut');

});


/*
|--------------------------------------------------------------------------
| PENGAJUAN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Pengajuan milik user
    Route::get('/pengajuan', [PengajuanController::class, 'index'])
        ->name('pengajuan.index');

    // Kirim pengajuan
    Route::post('/pengajuan', [PengajuanController::class, 'store'])
        ->name('pengajuan.store');

    // Admin melihat semua pengajuan
    Route::get('/admin/pengajuan', [PengajuanController::class, 'admin'])
        ->name('pengajuan.admin');

    // Terima pengajuan
    Route::put('/admin/pengajuan/{id}/terima', [PengajuanController::class, 'terima'])
        ->name('pengajuan.terima');

    // Tolak pengajuan
    Route::put('/admin/pengajuan/{id}/tolak', [PengajuanController::class, 'tolak'])
        ->name('pengajuan.tolak');

});


/*
|--------------------------------------------------------------------------
| REPORT
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Laporan milik user
    Route::get('/report', [ReportController::class, 'index'])
        ->name('report.index');

    // Kirim laporan
    Route::post('/report', [ReportController::class, 'store'])
        ->name('report.store');

    // Admin melihat semua laporan
    Route::get('/admin/report', [ReportController::class, 'admin'])
        ->name('report.admin');

    // Update status laporan
    Route::put('/admin/report/{id}/status', [ReportController::class, 'updateStatus'])
        ->name('report.updateStatus');

});


/*
|--------------------------------------------------------------------------
| NOTIFIKASI
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Semua notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])
        ->name('notifikasi.index');

    // Tandai satu notifikasi sudah dibaca
    Route::put('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])
        ->name('notifikasi.baca');

    // Tandai semua sudah dibaca
    Route::put('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])
        ->name('notifikasi.bacaSemua');

    // Hapus notifikasi
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])
        ->name('notifikasi.destroy');

});
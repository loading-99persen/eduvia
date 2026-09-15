<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->increments('id_pengajuan');

            $table->unsignedInteger('id_user');

            $table->unsignedInteger('id_komunitas')->nullable();

            $table->enum('tipe', [
                'komunitas',
                'webinar'
            ]);

            $table->string('judul', 100);

            $table->text('deskripsi')->nullable();

            $table->string('kategori', 50)->nullable();

            $table->string('nama_komunitas', 100)->nullable();

            $table->string('gambar', 255)->nullable();

            $table->text('alasan')->nullable();

            $table->string('pembicara', 100)->nullable();

            $table->date('tanggal')->nullable();

            $table->time('waktu')->nullable();

            $table->string('foto', 255)->nullable();

            $table->string('link_meeting', 255)->nullable();

            $table->enum('status', [
                'proses',
                'disetujui',
                'ditolak'
            ])->default('proses');

            $table->text('catatan_admin')->nullable();

            $table->dateTime('tanggal_pengajuan');

            $table->dateTime('diproses_pada')->nullable();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_komunitas')
                ->references('id_komunitas')
                ->on('komunitas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
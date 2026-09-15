<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id_reports');

            $table->unsignedInteger('id_user');

            $table->enum('tipe_target', [
                'post',
                'komentar',
                'user',
                'komunitas',
                'webinar'
            ]);

            $table->unsignedInteger('id_target');

            $table->text('alasan');

            $table->enum('status', [
                'proses',
                'diterima',
                'ditolak'
            ])->default('proses');

            $table->text('tanggapan_admin')->nullable();

            $table->dateTime('dibuat_pada');

            $table->dateTime('diproses_pada')->nullable();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
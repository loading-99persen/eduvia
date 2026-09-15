<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->increments('id_notifikasi');

            $table->unsignedInteger('id_user');

            $table->string('judul', 100);

            $table->text('pesan');

            $table->enum('tipe', [
                'balasan_postingan',
                'like',
                'mention',
                'aktivitas_komunitas',
                'pengajuan',
                'webinar',
                'jadwal',
                'sistem'
            ]);

            $table->boolean('dibaca')->default(false);

            $table->dateTime('dibuat_pada');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
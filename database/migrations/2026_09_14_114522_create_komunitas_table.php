<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komunitas', function (Blueprint $table) {
            $table->increments('id_komunitas');

            $table->unsignedInteger('id_leader');

            $table->string('nama_komunitas', 100);

            $table->text('deskripsi')->nullable();

            $table->string('kategori', 50)->nullable();

            $table->string('gambar', 255)->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            $table->dateTime('dibuat_pada');

            $table->foreign('id_leader')
                ->references('id_user')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komunitas');
    }
};
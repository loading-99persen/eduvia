<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil', function (Blueprint $table) {
            $table->increments('id_profil');

            $table->unsignedInteger('id_user');

            $table->string('nama_lengkap', 100);

            $table->string('photo', 255)->nullable();

            $table->text('bio')->nullable();

            $table->date('tanggal_lahir')->nullable();

            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ])->nullable();

            $table->string('tingkat_pendidikan', 50)->nullable();

            $table->string('institusi', 100)->nullable();

            $table->text('media_sosial')->nullable();

            $table->unique('id_user');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil');
    }
};
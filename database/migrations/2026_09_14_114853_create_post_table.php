<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post', function (Blueprint $table) {
            $table->increments('id_post');

            $table->unsignedInteger('id_user');

            $table->unsignedInteger('id_komunitas');

            $table->text('konten');

            $table->string('gambar', 255)->nullable();

            $table->string('file', 255)->nullable();

            $table->dateTime('dibuat_pada');

            $table->dateTime('diperbarui_pada')->nullable();

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
        Schema::dropIfExists('post');
    }
};
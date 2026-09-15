<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komentar', function (Blueprint $table) {
            $table->increments('id_komen');

            $table->unsignedInteger('id_post');

            $table->unsignedInteger('id_user');

            $table->text('komentar');

            $table->dateTime('dibuat_pada');

            $table->foreign('id_post')
                ->references('id_post')
                ->on('post')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komentar');
    }
};
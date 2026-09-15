<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->increments('id_like');

            $table->unsignedInteger('id_post');

            $table->unsignedInteger('id_user');

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

            $table->unique([
                'id_post',
                'id_user'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
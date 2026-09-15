<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan', function (Blueprint $table) {
            $table->increments('id_pesan');

            $table->unsignedInteger('id_rc');

            $table->unsignedInteger('id_user');

            $table->text('pesan');

            $table->dateTime('dikirim_pada');

            $table->foreign('id_rc')
                ->references('id_rc')
                ->on('chatroom')
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
        Schema::dropIfExists('pesan');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatroom', function (Blueprint $table) {
            $table->increments('id_rc');

            $table->unsignedInteger('id_komunitas');

            $table->string('nama', 100);

            $table->dateTime('dibuat_pada');

            $table->foreign('id_komunitas')
                ->references('id_komunitas')
                ->on('komunitas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatroom');
    }
};
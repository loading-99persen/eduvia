<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_komunitas', function (Blueprint $table) {
            $table->increments('id_memkom');

            $table->unsignedInteger('id_komunitas');

            $table->unsignedInteger('id_user');

            $table->enum('role', [
                'leader',
                'member'
            ])->default('member');

            $table->dateTime('bergabung_pada');

            $table->foreign('id_komunitas')
                ->references('id_komunitas')
                ->on('komunitas')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique([
                'id_komunitas',
                'id_user'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_komunitas');
    }
};
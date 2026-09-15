<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id_user');

            $table->string('email', 100)->unique();

            $table->string('password', 255);

            $table->unsignedInteger('id_role');

            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            $table->foreign('id_role')
                ->references('id_role')
                ->on('roles')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
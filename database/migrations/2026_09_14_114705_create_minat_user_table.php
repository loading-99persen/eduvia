<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minat_user', function (Blueprint $table) {
            $table->increments('id_userminat');

            $table->unsignedInteger('id_user');

            $table->unsignedInteger('id_minat');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_minat')
                ->references('id_minat')
                ->on('minat')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique([
                'id_user',
                'id_minat'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minat_user');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minat', function (Blueprint $table) {
            $table->increments('id_minat');

            $table->string('nama_minat', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minat');
    }
};
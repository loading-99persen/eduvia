<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar', function (Blueprint $table) {
            $table->increments('id_webinar');

            $table->unsignedInteger('id_leader');

            $table->unsignedInteger('id_komunitas');

            $table->string('judul', 100);

            $table->text('deskripsi')->nullable();

            $table->string('pembicara', 100)->nullable();

            $table->date('tanggal');

            $table->time('waktu');

            $table->string('foto', 255)->nullable();

            $table->string('kategori', 50)->nullable();

            $table->string('link_meeting', 255)->nullable();

            $table->enum('status', [
                'akan_datang',
                'berlangsung',
                'selesai',
                'dibatalkan'
            ])->default('akan_datang');

            $table->dateTime('dibuat_pada');

            $table->foreign('id_leader')
                ->references('id_user')
                ->on('users')
                ->onDelete('restrict')
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
        Schema::dropIfExists('webinar');
    }
};
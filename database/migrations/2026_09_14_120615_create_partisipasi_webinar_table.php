<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partisipasi_webinar', function (Blueprint $table) {
            $table->increments('id_parnar');

            $table->unsignedInteger('id_webinar');

            $table->unsignedInteger('id_user');

            $table->dateTime('bergabung_pada');

            $table->foreign('id_webinar')
                ->references('id_webinar')
                ->on('webinar')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique([
                'id_webinar',
                'id_user'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partisipasi_webinar');
    }
};
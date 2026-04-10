<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('judul');              // Judul Foto/Kegiatan
            $table->text('deskripsi')->nullable(); // Penjelasan singkat
            $table->string('foto');               // Path lokasi file gambar
            $table->timestamps();                 // Waktu upload
        });
    }

    public function down()
    {
        Schema::dropIfExists('galleries');
    }
};
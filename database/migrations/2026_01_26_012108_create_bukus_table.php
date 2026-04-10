<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membuat tabel kategori dulu
    Schema::create('kategoris', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kategori');
        $table->timestamps();
    });

        Schema::create('bukus', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_buku')->unique();
        $table->string('judul');
        $table->string('pengarang');
        $table->string('penerbit');
        $table->year('tahun');
        $table->foreignId('kategori_id')->constrained('kategoris'); // Relasi
        $table->integer('jumlah_eksemplar');
        $table->string('kondisi'); 
        $table->string('lokasi_lemari');
        $table->string('baris_rak');
        $table->timestamps(); // Menambahkan kolom created_at & updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};

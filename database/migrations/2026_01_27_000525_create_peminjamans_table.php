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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('bukus'); // Menghubungkan ke tabel buku
        $table->string('nama_peminjam');
        $table->integer('jumlah_pinjam')->default(1); // Jumlah buku yang dibawa
        $table->string('no_hp')->nullable(); // Nomor HP peminjam
        $table->date('tanggal_pinjam');
        $table->date('tanggal_kembali'); // Target 3 hari
        $table->date('tanggal_dikembalikan')->nullable(); // Realisasi kembali
        $table->enum('status', ['dipinjam', 'kembali'])->default('dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};

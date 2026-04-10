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
    Schema::create('museum_profiles', function (Blueprint $table) {
        $table->id();
        
        // --- BAGIAN 1: IDENTITAS (Untuk Footer) ---
        $table->string('nama_instansi')->nullable();
        $table->text('alamat')->nullable();
        $table->string('email')->nullable();
        $table->string('telepon')->nullable();
        $table->text('jam_operasional')->nullable(); // Bisa dipakai di Footer & Halaman Profil
        $table->text('kontak')->nullable(); // Opsional, jika butuh kontak tambahan
        
        // --- BAGIAN 2: KONTEN (Untuk Halaman Tentang Kami) ---
        $table->longText('sejarah')->nullable();
        $table->text('visi')->nullable();
        $table->text('misi')->nullable();
        $table->text('fasilitas')->nullable();
        $table->string('url_koleksi_eksternal')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('museum_profiles');
    }
};

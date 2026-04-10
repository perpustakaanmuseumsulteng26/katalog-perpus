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
        Schema::create('library_profiles', function (Blueprint $table) {
            $table->id();
            $table->text('sejarah')->nullable();
        $table->text('visi')->nullable();
        $table->text('misi')->nullable();
        $table->text('layanan')->nullable(); // Kolom baru khusus perpus
        $table->text('jam_operasional')->nullable();
        $table->text('peraturan')->nullable(); // Kolom baru khusus perpus
        $table->text('kontak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_profiles');
    }
};

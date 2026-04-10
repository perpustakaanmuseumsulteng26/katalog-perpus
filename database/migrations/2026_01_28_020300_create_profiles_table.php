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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // Area Museum
        $table->text('sejarah_museum');
        $table->text('visi_museum');
        $table->text('misi_museum');
        
        // Area Perpustakaan
        $table->text('sejarah_perpus');
        $table->text('visi_perpus');
        $table->text('misi_perpus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

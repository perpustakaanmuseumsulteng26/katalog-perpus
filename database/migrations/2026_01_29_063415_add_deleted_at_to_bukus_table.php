<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bukus', function (Blueprint $table) {
            // Menambahkan kolom deleted_at
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::table('bukus', function (Blueprint $table) {
            // Menghapus kolom jika rollback
            $table->dropSoftDeletes(); 
        });
    }
};
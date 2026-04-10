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
    Schema::table('museum_profiles', function (Blueprint $table) {
        // Menambahkan kolom yang kurang
        $table->text('layanan')->nullable()->after('misi');
        $table->text('info_kunjungan')->nullable()->after('layanan');
        $table->text('gmaps_link')->nullable()->after('email'); // Untuk Iframe Google Maps
    });
}

public function down(): void
{
    Schema::table('museum_profiles', function (Blueprint $table) {
        $table->dropColumn(['layanan', 'info_kunjungan', 'gmaps_link']);
    });
}
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Kita pakai DB facade biar cepat

class MuseumProfileSeeder extends Seeder
{
    public function run()
    {
        DB::table('museum_profiles')->insert([
            // Identitas (Footer)
            'nama_instansi' => 'UPT. Taman Budaya dan Museum Sulawesi Tengah',
            'alamat' => 'Jl. Kemiri No. 23, Palu Barat, Sulawesi Tengah',
            'email' => 'museum.sulteng@example.go.id',
            'telepon' => '(0451) 421333',
            'jam_operasional' => "Senin - Kamis: 08:00 - 15:30\nJumat: 08:00 - 16:00",
            
            // Konten (Sejarah/Visi/Misi)
            'sejarah' => 'Museum Sulawesi Tengah didirikan pada tahun...',
            'visi' => 'Terwujudnya museum yang informatif...',
            'misi' => '1. Melestarikan benda budaya...',
            'url_koleksi_eksternal' => 'https://koleksi.kemdikbud.go.id',
            
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
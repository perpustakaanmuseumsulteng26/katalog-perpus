<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Tambahkan ini agar Hash bisa terbaca

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ganti data di bawah ini dengan informasi asli sistem Anda
        User::create([
            'name' => 'Admin Museum',
            'email' => 'perpustakaanmuseumsulteng26@gmail.com', // MASUKKAN GMAIL KHUSUS ANDA DI SINI
            'password' => Hash::make('perpustakaan2026copY'), // Tentukan password awal admin
            // Jika Anda punya kolom 'role', tambahkan di bawah:
            // 'role' => 'admin', 
        ]);
    }
}
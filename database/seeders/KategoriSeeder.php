<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = ['000-KARYA UMUM', '100-FILSAFAT', '200-AGAMA', '300-ILMU ILMU SOSIAL', '400-BAHASA', '500-ILMU ILMU MURNI', '600-TEKNOLOGI', '700-KESENIAN', '800-KESUSASTRAAN', '900-GEOGRAFI DAN SEJARAH'];
    foreach ($kategori as $k) {
            DB::table('kategoris')->insert([  // Sekarang tidak perlu pakai tanda \ lagi
                'nama_kategori' => $k,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryProfile extends Model
{
    use HasFactory;

    protected $table = 'library_profiles'; // Pastikan nama tabel benar

    // OPSI TERBAIK: Gunakan $guarded
    // Artinya: "Semua kolom BOLEH diisi, KECUALI kolom 'id'"
    protected $guarded = ['id']; 
    
    // (Jangan gunakan $fillable jika kolomnya banyak, nanti capek ngetiknya dan sering lupa)
}
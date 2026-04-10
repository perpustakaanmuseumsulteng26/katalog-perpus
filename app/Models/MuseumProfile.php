<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MuseumProfile extends Model
{
    use HasFactory;

    protected $table = 'museum_profiles'; // Pastikan nama tabel benar

    protected $fillable = [
        'nama_instansi', 
        'alamat', 
        'email', 
        'telepon', 
        'jam_operasional', 
        'kontak',
        'sejarah', 
        'visi', 
        'misi', 
        'fasilitas', 
        'url_koleksi_eksternal'
    ];
    protected $casts = [
        'url_koleksi_eksternal' => 'array',
    ];
}
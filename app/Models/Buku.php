<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Jika pakai soft delete

class Buku extends Model
{
    use HasFactory, SoftDeletes; // Sesuaikan jika pakai SoftDeletes

    protected $table = 'bukus';

    // --- PERHATIKAN BAGIAN INI ---
    protected $fillable = [
        'nomor_buku',       // <--- WAJIB ADA DI SINI
        'judul',
        'foto',
        'pengarang',
        'penerbit',
        'tahun',     // Sesuaikan dengan nama kolom di database (tahun/tahun_terbit)
        'kategori_id',
        'jumlah_eksemplar',
        'stok_tersedia',
        'lokasi_lemari',
        'baris_rak',
        'kondisi',

    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
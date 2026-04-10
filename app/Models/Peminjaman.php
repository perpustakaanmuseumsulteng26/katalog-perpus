<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';
    protected $fillable = [
        'buku_id', 
        'nama_peminjam', 
        'jumlah_pinjam',
        'no_hp',
        'tanggal_pinjam', 
        'tanggal_kembali', 
        'tanggal_dikembalikan', 
        'status'
    ];

    // Menghubungkan ke data Buku
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
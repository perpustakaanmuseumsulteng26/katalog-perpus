<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori'];

    /**
     * Relasi: Satu Kategori memiliki banyak Buku
     */
    public function buku()
    {
        return $this->hasMany(Buku::class, 'kategori_id');
    }
}
<?php

namespace App\Imports;

use App\Models\Buku;
use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading; // <--- 1. TAMBAH INI

// 2. TAMBAH ", WithChunkReading" DI SINI
class BukuImport implements ToModel, WithHeadingRow, WithChunkReading 
{
    public function model(array $row)
    {
        // ... (Kode logika kategori Anda tetap sama seperti sebelumnya) ...
        $kategori = Kategori::where('nama_kategori', 'like', '%' . $row['kategori'] . '%')->first();
        $kategori_id = $kategori ? $kategori->id : 1; 

        return new Buku([
            'nomor_buku'     => $row['nomor_buku'],
            'judul'          => $row['judul'],
            'pengarang'      => $row['pengarang'],
            'penerbit'       => $row['penerbit'],
            'tahun'          => $row['tahun'],
            'jumlah_eksemplar' => $row['jumlah'],
            'stok_tersedia'  => $row['jumlah'],
            'kategori_id'    => $kategori_id,
            'lokasi_lemari'  => $row['lemari'],
            'baris_rak'      => $row['rak'],
            'kondisi'        => 'baik',
        ]);
    }

    // 3. TAMBAHKAN FUNGSI INI DI PALING BAWAH
    public function chunkSize(): int
    {
        return 100; // Proses setiap 100 baris, lalu istirahat sebentar
    }
}
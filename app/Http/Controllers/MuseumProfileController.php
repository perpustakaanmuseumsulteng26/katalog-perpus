<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MuseumProfile; 

class MuseumProfileController extends Controller
{
    /**
     * Menampilkan Form Edit untuk Admin
     */
    public function edit()
    {
        $profil = MuseumProfile::first();
        
        if (!$profil) {
            $profil = new MuseumProfile();
        }

        // --- PERBAIKAN DI SINI ---
        // Mengarah ke file: resources/views/museum/edit.blade.php
        return view('museum.edit', compact('profil'));
    }

    /**
     * Memproses Penyimpanan Data Lengkap
     */
    public function update(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            // Wajib diisi (Identitas Footer)
            'nama_instansi'   => 'required',
            'alamat'          => 'required',
            'email'           => 'required|email',
            'telepon'         => 'required',
            
            // Boleh kosong (Konten Halaman)
            'jam_operasional' => 'nullable', 
            'sejarah'         => 'nullable',
            'visi'            => 'nullable',
            'misi'            => 'nullable',
            'fasilitas'       => 'nullable',
            
            // --- KOLOM BARU ---
            'layanan'         => 'nullable',
            'info_kunjungan'  => 'nullable',
            'gmaps_link'      => 'nullable',
            
            // Validasi Array Links (Dinamis)
            'links'           => 'nullable|array', 
            'links.*.label'   => 'nullable|string',
            'links.*.url'     => 'nullable|url',
        ]);

        // 2. Simpan atau Update Data
        MuseumProfile::updateOrCreate(
            ['id' => 1], // Selalu update baris ID 1
            [
                // Identitas
                'nama_instansi'   => $request->nama_instansi,
                'alamat'          => $request->alamat,
                'email'           => $request->email,
                'telepon'         => $request->telepon,
                'jam_operasional' => $request->jam_operasional,
                'gmaps_link'      => $request->gmaps_link, 
                
                // Profil & Konten
                'sejarah'         => $request->sejarah,
                'visi'            => $request->visi,
                'misi'            => $request->misi,
                'fasilitas'       => $request->fasilitas,
                'layanan'         => $request->layanan,        
                'info_kunjungan'  => $request->info_kunjungan, 
                
                // Simpan Array Links ke kolom url_koleksi_eksternal
                'url_koleksi_eksternal' => $request->links,
            ]
        );

        return redirect()->back()->with('success', 'Data Profil Museum Lengkap Berhasil Diperbarui!');
    }
    
    /**
     * Menampilkan Halaman Profil untuk Pengunjung (Publik)
     */
    public function show()
    {
        $profil = MuseumProfile::first();
        
        if (!$profil) {
            $profil = new MuseumProfile();
            $profil->nama_instansi = 'Nama Museum Belum Diatur';
            $profil->sejarah = 'Data sejarah belum diinput oleh admin.';
        }

        // Mengarah ke file: resources/views/museum/show.blade.php
        return view('museum.show', compact('profil'));
    }
}
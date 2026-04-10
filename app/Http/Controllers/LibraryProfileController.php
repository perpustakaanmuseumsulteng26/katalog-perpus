<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibraryProfile;

class LibraryProfileController extends Controller
{
    // Menampilkan Form Edit untuk Admin
    public function edit()
    {
        $profil = LibraryProfile::first() ?? new LibraryProfile();
        // Kita akan buat folder 'library' nanti
        return view('library.edit', compact('profil'));
    }

    // Menyimpan Data
    public function update(Request $request)
    {
        $request->validate([
            'sejarah' => 'nullable',
            // Validasi lain opsional, text boleh kosong
        ]);

        LibraryProfile::updateOrCreate(
            ['id' => 1],
            $request->except(['_token'])
        );

        return redirect()->back()->with('success', 'Profil Perpustakaan berhasil diperbarui!');
    }

    // Menampilkan Halaman Publik
    public function show()
    {
        $profil = LibraryProfile::first();
        return view('library.show', compact('profil'));
    }
}
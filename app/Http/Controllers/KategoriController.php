<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index() {
        $kategoris = Kategori::all();
        return view('kategori.index', compact('kategoris'));
    }

    public function store(Request $request) {
        $request->validate(['nama_kategori' => 'required|unique:kategoris']);
        Kategori::create($request->all());
        return redirect()->back()->with('success', 'Kategori berhasil ditambah!');
    }

    public function destroy($id)
{
    $kategori = \App\Models\Kategori::findOrFail($id);

    // Jika ada buku yang memakai kategori ini, jangan hapus
    if ($kategori->buku()->count() > 0) {
        return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh beberapa buku!');
    }

    $kategori->delete();
    return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_kategori' => 'required|unique:kategoris,nama_kategori,' . $id
    ]);

    $kategori = Kategori::findOrFail($id);
    $kategori->update([
        'nama_kategori' => $request->nama_kategori
    ]);

    return redirect()->back()->with('success', 'Nama kategori berhasil diubah!');
}
}
<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * UNTUK PENGUNJUNG
     * Menampilkan semua koleksi foto di halaman publik.
     */
    public function index()
    {
        // Mengambil foto terbaru agar muncul di urutan paling atas
        $galleries = Gallery::latest()->get();
        return view('gallery.index', compact('galleries'));
    }

    /**
     * UNTUK ADMIN
     * Menampilkan dashboard pengelolaan galeri.
     */
    public function adminIndex()
    {
        $galleries = Gallery::latest()->get();
        return view('admin.gallery.index', compact('galleries'));
    }

    /**
     * PROSES SIMPAN FOTO
     * Menangani upload gambar ke storage/app/public/gallery.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
            'deskripsi' => 'nullable|string'
        ]);

        // Simpan file ke folder 'gallery' di dalam disk 'public'
        // Path yang disimpan di DB: gallery/nama_file.jpg
        $path = $request->file('foto')->store('gallery', 'public');

        // Simpan data ke database
        Gallery::create([
            'judul'     => $request->judul,
            'foto'      => $path,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Foto kegiatan berhasil diunggah ke galeri!');
    }

    /**
     * PROSES HAPUS FOTO
     * Menghapus file gambar dari penyimpanan dan menghapus record dari database.
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        
        // Hapus file fisik dari folder storage agar tidak menumpuk sampah
        if (Storage::disk('public')->exists($gallery->foto)) {
            Storage::disk('public')->delete($gallery->foto);
        }
        
        // Hapus record dari database
        $gallery->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus secara permanen!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Panduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PanduanController extends Controller
{
    // ==========================================
    // BAGIAN 1: PENAMPIL (VIEWER)
    // ==========================================

    // 1. Tampilan untuk PENGUNJUNG (Publik)
    public function index() {
        // Ambil data khusus pengunjung, diurutkan sesuai angka 'urutan' (1, 2, 3...)
        $panduans = Panduan::where('target', 'pengunjung')
                           ->orderBy('urutan', 'asc') 
                           ->get();
                           
        return view('panduan.pengunjung', compact('panduans'));
    }

    // 2. Tampilan untuk ADMIN (Internal / Cara Pakai Sistem)
    public function adminView() {
        // Ambil data khusus admin, diurutkan sesuai angka 'urutan'
        $panduans = Panduan::where('target', 'admin')
                           ->orderBy('urutan', 'asc')
                           ->get();

        return view('panduan.admin_view', compact('panduans'));
    }


    // ==========================================
    // BAGIAN 2: CRUD (KELOLA DATA)
    // ==========================================

    // 3. Halaman Tabel Daftar Panduan (Untuk Admin Mengelola)
    public function manage() {
        // Kita urutkan berdasarkan Target dulu, baru Urutannya
        $panduans = Panduan::orderBy('target', 'desc') // Admin dulu atau Pengunjung dulu
                           ->orderBy('urutan', 'asc')
                           ->get();

        return view('admin.panduan.index', compact('panduans'));
    }

    // 4. Tampilkan Form Tambah
    public function create() {
        return view('admin.panduan.create');
    }

    // 5. Proses SIMPAN Data Baru
   public function store(Request $request) {
        $request->validate([
            'judul'     => 'required',
            'target'    => 'required',
            'urutan'    => 'required|numeric',
            'deskripsi' => 'nullable',
            'video'     => 'nullable|mimes:mp4,mov,avi|max:51200',
        ]);

        // 1. Ambil semua data input
        $data = $request->all();

        // 2. Proses Upload Video
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Simpan file ke folder
            $file->move(public_path('videos'), $filename);
            
            // Masukkan alamat file ke kolom 'video_path'
            $data['video_path'] = 'videos/' . $filename;
        }

        // === PERBAIKAN DI SINI ===
        // 3. Hapus input 'video' (file mentah) dari array data
        // agar Laravel tidak mencoba menyimpannya ke kolom 'video' yang tidak ada.
        unset($data['video']); 

        // 4. Simpan ke Database
        Panduan::create($data);

        return redirect()->route('panduan.manage')->with('success', 'Panduan berhasil ditambahkan!');
    }
    // 6. Tampilkan Form Edit
    public function edit($id) {
        $panduan = Panduan::findOrFail($id);
        return view('admin.panduan.edit', compact('panduan'));
    }

    // 7. Proses UPDATE Data
    public function update(Request $request, $id) {
        $panduan = Panduan::findOrFail($id);

        $request->validate([
            'judul'     => 'required',
            'target'    => 'required',
            'urutan'    => 'required|numeric',
            'video'     => 'nullable|mimes:mp4,mov,avi|max:51200',
        ]);

        $data = $request->all();

        // Cek jika user mengupload video baru
        if ($request->hasFile('video')) {
            // 1. Hapus video lama dari folder (agar server tidak penuh)
            if ($panduan->video_path && file_exists(public_path($panduan->video_path))) {
                unlink(public_path($panduan->video_path));
            }

            // 2. Upload video baru
            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('videos'), $filename);
            $data['video_path'] = 'videos/' . $filename;
        }

        $panduan->update($data);

        return redirect()->route('panduan.manage')->with('success', 'Data panduan berhasil diperbarui!');
    }

    // 8. Proses HAPUS Data
    public function destroy($id) {
        $panduan = Panduan::findOrFail($id);
        
        // Hapus file fisik video jika ada
        if ($panduan->video_path && file_exists(public_path($panduan->video_path))) {
            unlink(public_path($panduan->video_path));
        }

        $panduan->delete();

        return redirect()->back()->with('success', 'Panduan telah dihapus.');
    }
}
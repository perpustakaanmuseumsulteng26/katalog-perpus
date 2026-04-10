<?php

namespace App\Http\Controllers;

use App\Imports\BukuImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\MuseumProfile; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage; 

class BukuController extends Controller
{
    // --- DASHBOARD ADMIN ---
    public function dashboard()
    {
        $hari_ini = now()->format('Y-m-d');
        $besok = now()->addDay()->format('Y-m-d');

        $peringatan_tenggat = Peminjaman::where('status', 'dipinjam')
            ->whereBetween('tanggal_kembali', [$hari_ini, $besok])
            ->with('buku')
            ->get();

        $terlambat = Peminjaman::where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', $hari_ini)
            ->with('buku')
            ->get();

        $total_buku = Buku::count();
        $buku_dipinjam = Peminjaman::where('status', 'dipinjam')->sum('jumlah_pinjam'); 

        return view('admin.dashboard', compact('peringatan_tenggat', 'terlambat', 'total_buku', 'buku_dipinjam'));
    }

    // --- DAFTAR BUKU (ADMIN) + SEARCH ---
    public function index(Request $request) {
        
        $query = Buku::with('kategori')->latest();

        // Logika Pencarian Admin
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('pengarang', 'like', "%$search%")
                  ->orWhere('nomor_buku', 'like', "%$search%");
            });
        }

        $bukus = $query->paginate(10)->withQueryString();

        return view('buku.index', compact('bukus'));
    }

    // --- FORM TAMBAH BUKU ---
    public function create() {
        $kategoris = Kategori::all();
        return view('buku.create', compact('kategoris'));
    }

    // --- SIMPAN BUKU ---
    public function store(Request $request)
    {
        $request->validate([
            'nomor_buku'       => 'required', // Boleh duplikat
            'judul'            => 'required',
            'pengarang'        => 'required',
            'penerbit'         => 'required',
            'tahun'            => 'nullable', // Boleh kosong
            'jumlah_eksemplar' => 'required|numeric|min:1', 
            'kategori_id'      => 'required',
            'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $data = $request->except(['_token', 'foto']);
        $data['stok_tersedia'] = $request->jumlah_eksemplar;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('cover_buku', 'public');
            $data['foto'] = $path;
        }

        Buku::create($data);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil disimpan!');
    }

    // --- FORM EDIT ---
    public function edit($id) {
       $buku = Buku::findOrFail($id);
       $kategoris = Kategori::all();
       return view('buku.edit', compact('buku', 'kategoris'));
    }

    // --- UPDATE BUKU (FIX SYNTAX ERROR) ---
    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $request->validate([
            'nomor_buku' => 'required', // <--- PERBAIKAN: Hapus .$id karena unique sudah dihapus
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun' => 'nullable', // Boleh kosong
            'kategori_id' => 'required',
            'jumlah_eksemplar' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hitung selisih stok
        $selisih = $request->jumlah_eksemplar - $buku->jumlah_eksemplar;

        $dataUpdate = [
            'nomor_buku' => $request->nomor_buku,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'penerbit' => $request->penerbit,
            'tahun' => $request->tahun,
            'kategori_id' => $request->kategori_id,
            'jumlah_eksemplar' => $request->jumlah_eksemplar,
            'stok_tersedia' => $buku->stok_tersedia + $selisih,
            'kondisi' => $request->kondisi,
            'lokasi_lemari' => $request->lokasi_lemari,
            'baris_rak' => $request->baris_rak,
        ];

        if ($request->hasFile('foto')) {
            if ($buku->foto && Storage::disk('public')->exists($buku->foto)) {
                Storage::disk('public')->delete($buku->foto);
            }
            $path = $request->file('foto')->store('cover_buku', 'public');
            $dataUpdate['foto'] = $path;
        }

        $buku->update($dataUpdate);

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    // --- SOFT DELETE ---
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete(); 
        return redirect()->back()->with('success', 'Buku berhasil diarsipkan (Soft Delete).');
    }

    // --- SAMPAH ---
    public function trash()
    {
        $bukus = Buku::onlyTrashed()->with('kategori')->get();
        return view('buku.trash', compact('bukus'));
    }

    // --- RESTORE ---
    public function restore($id)
    {
        $buku = Buku::withTrashed()->findOrFail($id);
        $buku->restore(); 
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dikembalikan ke daftar aktif.');
    }

    // --- HAPUS PERMANEN ---
    public function forceDelete($id)
    {
        $buku = Buku::withTrashed()->findOrFail($id);
        
        if ($buku->foto && Storage::disk('public')->exists($buku->foto)) {
            Storage::disk('public')->delete($buku->foto);
        }

        $buku->forceDelete(); 
        return redirect()->route('buku.trash')->with('success', 'Buku telah dihapus secara permanen.');
    }

    // --- HALAMAN PENGUNJUNG ---
    public function search(Request $request) {
        $q = $request->q;
        $kategori = $request->kategori;

        $bukus = Buku::when($q, function($query) use ($q) {
            $query->where('judul', 'like', "%$q%")
                  ->orWhere('pengarang', 'like', "%$q%");
        })->when($kategori, function($query) use ($kategori) {
            $query->where('kategori_id', $kategori);
        })
        ->latest()
        ->paginate(8); 

        $kategoris = Kategori::all();
        $profil = MuseumProfile::first();
        $sliders = collect([]); 

        return view('welcome', compact('bukus', 'kategoris','profil', 'sliders'));
    }

    // --- IMPORT EXCEL ---
    public function import(Request $request) 
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new BukuImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data buku berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Gagal import: ' . $e->getMessage()]);
        }
    }

    // --- PREVIEW LAPORAN ---
    public function previewBuku()
    {
       $bukus = Buku::join('kategoris', 'bukus.kategori_id', '=', 'kategoris.id')
                     ->orderBy('kategoris.nama_kategori', 'asc')
                     ->select('bukus.*')
                     ->with('kategori')
                     ->get();

       return view('admin.laporan.preview_buku', compact('bukus'));
    }

    // --- DOWNLOAD PDF ---
    public function downloadBuku()
    {
        $bukus = Buku::join('kategoris', 'bukus.kategori_id', '=', 'kategoris.id')
                     ->orderBy('kategoris.nama_kategori', 'asc') 
                     ->select('bukus.*') 
                     ->with('kategori')
                     ->get();
        
        $pdf = Pdf::loadView('admin.laporan_buku_pdf', compact('bukus')); 
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-koleksi-buku.pdf');
    }
}
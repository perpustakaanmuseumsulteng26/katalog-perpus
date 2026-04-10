<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller
{
    // Menampilkan daftar buku yang MASIH dipinjam
    public function index()
    {
        $peminjamans = Peminjaman::with('buku')->where('status', 'dipinjam')->get();
        return view('peminjaman.index', compact('peminjamans'));
    }

    // Form input peminjaman
    public function create()
    {
        $bukus = Buku::where('stok_tersedia', '>', 0)->get();
        return view('peminjaman.create', compact('bukus'));
    }

    // Proses simpan data (Stok Berkurang)
    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required',
            'nama_peminjam' => 'required',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
        ]);

        $buku = Buku::findOrFail($request->buku_id);

        if ($buku->stok_tersedia < $request->jumlah_pinjam) {
            return back()->with('error', 'Stok tidak cukup!');
        }

        Peminjaman::create([
            'buku_id' => $request->buku_id,
            'nama_peminjam' => $request->nama_peminjam,
            'jumlah_pinjam' => $request->jumlah_pinjam,
            'no_hp' => $request->no_hp,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => date('Y-m-d', strtotime($request->tanggal_pinjam . ' +3 days')),
            'status' => 'dipinjam',
        ]);

        $buku->decrement('stok_tersedia', $request->jumlah_pinjam);

        return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dipinjam.');
    }

    // Proses pengembalian (Stok Bertambah)
    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'status' => 'kembali',
            'tanggal_dikembalikan' => now(), // Mencatat tanggal realisasi
        ]);

        $peminjaman->buku->increment('stok_tersedia', $peminjaman->jumlah_pinjam);

        return redirect()->route('peminjaman.index')->with('success', 'Buku telah kembali.');
    }
public function previewPeminjaman()
{
    // Mengambil data peminjaman beserta data buku terkait
    $peminjaman = Peminjaman::with('buku')->orderBy('tanggal_pinjam', 'desc')->get();
    
    return view('admin.laporan.preview_peminjaman', compact('peminjaman'));
}

public function downloadPeminjaman()
{
    // Mengambil data peminjaman beserta data buku terkait
    $peminjaman = Peminjaman::with('buku')->orderBy('tanggal_pinjam', 'desc')->get();
    
    $pdf = Pdf::loadView('admin.laporan_peminjaman_pdf', compact('peminjaman'));
    
    // Set kertas A4 Landscape agar muat banyak informasi
    return $pdf->setPaper('a4', 'landscape')->download('Laporan_Peminjaman_Buku.pdf');
}
}
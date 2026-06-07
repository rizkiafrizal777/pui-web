<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class StokController extends Controller
{
    /**
     * Tampilkan daftar stok semua produk.
     */
    public function index()
    {
        $produks = Produk::with('kategori')->orderBy('nama_produk')->get();
        return view('admin.stok.index', compact('produks'));
    }

    /**
     * Tambah stok produk.
     */
    public function tambah(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:9999',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->increment('stok', $request->jumlah);

        return back()->with('success', "Stok \"{$produk->nama_produk}\" berhasil ditambah {$request->jumlah} unit. Stok sekarang: {$produk->stok}.");
    }

    /**
     * Set / reset stok produk ke nilai tertentu.
     */
    public function set(Request $request, $id)
    {
        $request->validate([
            'stok' => 'required|integer|min:0|max:9999',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update(['stok' => $request->stok]);

        return back()->with('success', "Stok \"{$produk->nama_produk}\" berhasil diatur ke {$request->stok} unit.");
    }
}

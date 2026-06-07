<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Tampilkan daftar produk untuk API Mobile.
     */
    public function index()
    {
        // Mengambil semua produk dengan kategori
        $produk = Produk::with('kategori')->get();

        // Tambahkan URL lengkap untuk field foto agar bisa langsung dipakai di mobile
        $produk->transform(function ($item) {
            $item->foto = $item->foto
                ? asset('storage/' . $item->foto)
                : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil.',
            'data' => $produk
        ], 200);
    }
}

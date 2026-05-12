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
        // Mengambil semua produk yang tersedia
        $produk = Produk::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil.',
            'data' => $produk
        ], 200);
    }
}

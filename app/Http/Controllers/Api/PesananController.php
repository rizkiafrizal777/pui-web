<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PesananController extends Controller
{
    /**
     * Mengambil riwayat pesanan (khusus untuk user yang sedang login)
     */
    public function index(Request $request)
    {
        // $request->user() tersedia jika menggunakan middleware auth:sanctum
        $pesanans = Pesanan::with(['itemPesanan.produk'])
            ->where('id_pengguna', $request->user()->id)
            ->orderBy('tgl_pesanan', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pesanan berhasil diambil.',
            'data' => $pesanans
        ], 200);
    }

    // Melihat detail satu pesanan (struk)
    public function show(Request $request, $id)
    {
        $pesanan = Pesanan::with(['itemPesanan.produk', 'pengguna'])->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }

        // Opsional: Proteksi agar kasir hanya bisa melihat struknya sendiri
        // if ($pesanan->id_pengguna !== $request->user()->id && $request->user()->role !== 'admin') {
        //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        // }

        return response()->json([
            'success' => true,
            'message' => 'Detail pesanan berhasil diambil.',
            'data' => $pesanan
        ], 200);
    }

    // Proses simpan pesanan / checkout (API)
    public function store(Request $request)
    {
        // Validasi input dari mobile
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.id_produk'  => 'required|exists:produk,id',
            'items.*.jumlah'     => 'required|integer|min:1|max:99',
            'metode_bayar'       => 'required|in:tunai,qris,transfer',
            'no_meja'            => 'nullable|string|max:100',
            'uang_diterima'      => 'nullable|numeric|min:0', 
        ]);

        try {
            DB::beginTransaction();

            $totalHitung = 0;
            $itemsValid  = [];

            foreach ($request->items as $item) {
                // Ambil harga dari DB, bukan dari request
                $produk = Produk::findOrFail($item['id_produk']);

                if (!$produk->tersedia) {
                    throw new \Exception("Produk \"{$produk->nama_produk}\" sudah tidak tersedia.");
                }

                $hargaSatuan = (float) $produk->harga;
                $jumlah      = (int)   $item['jumlah'];
                $subtotal    = $hargaSatuan * $jumlah;

                $totalHitung += $subtotal;

                $itemsValid[] = [
                    'id_produk'    => $produk->id,
                    'jumlah'       => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal'     => $subtotal,
                ];
            }

            if ($totalHitung <= 0) {
                throw new \Exception('Total pesanan tidak valid (Rp 0).');
            }

            $uangDiterima = null;
            $kembalian    = null;

            if ($request->metode_bayar === 'tunai') {
                $uangDiterima = (float) $request->uang_diterima;

                if ($uangDiterima <= 0) {
                    throw new \Exception('Uang diterima harus diisi untuk pembayaran tunai.');
                }

                if ($uangDiterima < $totalHitung) {
                    throw new \Exception("Uang yang diterima kurang dari total harga.");
                }

                $kembalian = $uangDiterima - $totalHitung;
            }

            // Generate Kode Pesanan (#YYYY-NNN)
            $tahun      = Carbon::now()->format('Y');
            $lastPesanan = Pesanan::whereYear('tgl_pesanan', $tahun)->orderBy('id', 'desc')->first();
            $urutan = 1;

            if ($lastPesanan) {
                $parts = explode('-', $lastPesanan->kode_pesanan);
                if (count($parts) == 2) {
                    $urutan = intval($parts[1]) + 1;
                }
            }

            $kodePesanan = '#' . $tahun . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

            // Simpan Pesanan
            $pesanan = Pesanan::create([
                'kode_pesanan'  => $kodePesanan,
                'id_pengguna'   => $request->user()->id, // Diambil dari token
                'no_meja'       => $request->no_meja,
                'total_harga'   => $totalHitung,
                'uang_diterima' => $uangDiterima,
                'kembalian'     => $kembalian,
                'metode_bayar'  => $request->metode_bayar,
                'status'        => 'selesai',
                'tgl_pesanan'   => Carbon::now(),
            ]);

            // Simpan Item Pesanan
            foreach ($itemsValid as $item) {
                ItemPesanan::create([
                    'id_pesanan'   => $pesanan->id,
                    'id_produk'    => $item['id_produk'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            DB::commit();

            // Load relasi agar data yang dikembalikan lengkap untuk struk langsung
            $pesanan->load('itemPesanan.produk');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disimpan.',
                'data'    => $pesanan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }
}

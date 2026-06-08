<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosController extends Controller
{
    /**
     * Tampilkan halaman utama Kasir (POS)
     */
    public function index()
    {
        // Ambil semua produk yang tersedia
        $produk = Produk::with('kategori')->where('tersedia', true)->get();
        return view('pos.index', compact('produk'));
    }

    /**
     * Proses simpan pesanan dari AJAX
     */
    public function bayar(Request $request)
    {
        // ─── VALIDASI INPUT ───────────────────────────────────────────────────
        // HANYA validasi id_produk dan jumlah dari client.
        // harga_satuan, subtotal, dan total_harga DIABAIKAN — dihitung ulang di server.
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.id_produk'  => 'required|exists:produk,id',
            'items.*.jumlah'     => 'required|integer|min:1|max:99',
            'metode_bayar'       => 'required|in:tunai,qris,transfer',
            'no_meja'            => 'nullable|string|max:100',
            'uang_diterima'      => 'nullable|numeric|min:0', // Ditambahkan: hanya wajib jika tunai
        ]);

        try {
            DB::beginTransaction();

            // ─── HITUNG ULANG TOTAL DI SERVER ─────────────────────────────────
            // Tidak mempercayai harga dari request. Semua harga diambil dari DB.
            $totalHitung = 0;
            $itemsValid  = []; // Akan berisi data item dengan harga DARI DATABASE

            foreach ($request->items as $item) {
                // Ambil produk dari database — harga resmi, bukan dari client
                $produk = Produk::findOrFail($item['id_produk']);

                // Cek ketersediaan produk saat checkout terjadi
                if (!$produk->tersedia) {
                    throw new \Exception("Produk \"{$produk->nama_produk}\" sudah tidak tersedia.");
                }

                $jumlah = (int) $item['jumlah'];

                // Cek kecukupan stok
                if ($produk->stok < $jumlah) {
                    throw new \Exception("Stok \"{$produk->nama_produk}\" tidak mencukupi. Stok tersisa: {$produk->stok}.");
                }

                // Hitung subtotal menggunakan HARGA DARI DATABASE
                $hargaSatuan = (float) $produk->harga;
                $subtotal    = $hargaSatuan * $jumlah;

                // Akumulasi ke total keseluruhan
                $totalHitung += $subtotal;

                // Simpan ke array item valid dengan harga dari DB
                $itemsValid[] = [
                    'produk_obj'   => $produk, // referensi untuk kurangi stok setelah simpan
                    'id_produk'    => $produk->id,
                    'jumlah'       => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal'     => $subtotal,
                ];
            }

            // Validasi extra: total tidak boleh nol
            if ($totalHitung <= 0) {
                throw new \Exception('Total pesanan tidak valid (Rp 0). Periksa kembali item pesanan.');
            }

            // ─── HITUNG KEMBALIAN (khusus tunai) ──────────────────────────────
            $uangDiterima = null;
            $kembalian    = null;

            if ($request->metode_bayar === 'tunai') {
                $uangDiterima = (float) $request->uang_diterima;

                if ($uangDiterima <= 0) {
                    throw new \Exception('Uang diterima harus diisi untuk pembayaran tunai.');
                }

                if ($uangDiterima < $totalHitung) {
                    $kurang = number_format($totalHitung - $uangDiterima, 0, ',', '.');
                    throw new \Exception("Uang yang diterima kurang Rp {$kurang} dari total harga.");
                }

                $kembalian = $uangDiterima - $totalHitung; // Hitung kembalian di server
            }

            // ─── GENERATE KODE PESANAN (#YYYY-NNN) ────────────────────────────
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

            // ─── SIMPAN PESANAN ────────────────────────────────────────────────
            // Menggunakan $totalHitung (server) dan $kembalian (server)
            $pesanan = Pesanan::create([
                'kode_pesanan'  => $kodePesanan,
                'id_pengguna'   => Auth::id(),
                'no_meja'       => $request->no_meja,
                'total_harga'   => $totalHitung,   // dari server
                'uang_diterima' => $uangDiterima,  // null jika bukan tunai
                'kembalian'     => $kembalian,     // null jika bukan tunai
                'metode_bayar'  => $request->metode_bayar,
                'status'        => 'selesai',
                'tgl_pesanan'   => Carbon::now(),
            ]);

            // ─── SIMPAN ITEM PESANAN & KURANGI STOK ───────────────────────────
            // Menggunakan $itemsValid (harga dari DB), BUKAN $request->items langsung
            foreach ($itemsValid as $item) {
                ItemPesanan::create([
                    'id_pesanan'   => $pesanan->id,
                    'id_produk'    => $item['id_produk'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'], // dari DB
                    'subtotal'     => $item['subtotal'],     // dihitung server
                ]);

                // Kurangi stok produk
                $item['produk_obj']->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'redirect_url' => route('pos.selesai', ['id' => $pesanan->id]), // Diubah: ke halaman selesai
                'message'      => 'Pesanan berhasil disimpan!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Halaman konfirmasi pesanan selesai (dengan kembalian & tombol cetak)
     * Redirect target setelah bayar berhasil.
     */
    public function selesai($id)
    {
        $pesanan = Pesanan::with(['itemPesanan.produk', 'pengguna'])->findOrFail($id);
        return view('pos.selesai', compact('pesanan'));
    }

    /**
     * //Tampilkan halaman struk lama (masih diakses dari riwayat)
     */
    public function struk($id)
    {
        $pesanan = Pesanan::with(['itemPesanan.produk', 'pengguna'])->findOrFail($id);
        
        // Proteksi: Kasir hanya boleh lihat struk pesanannya sendiri? (Opsional)
        // Jika ingin admin bisa lihat semua, kita biarkan saja.

        return view('pos.struk', compact('pesanan'));
    }

    /**
     * Tampilkan halaman riwayat pesanan kasir
     */
    public function riwayat(Request $request)
    {
        // Ambil pesanan yang dibuat oleh kasir yang sedang login
        $pesanans = Pesanan::with(['itemPesanan'])
            ->where('id_pengguna', Auth::id())
            ->orderBy('tgl_pesanan', 'desc')
            ->paginate(15);

        return view('pos.riwayat', compact('pesanans'));
    }
}

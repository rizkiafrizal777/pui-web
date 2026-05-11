<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use App\Models\Produk;
use App\Models\Pengguna;
use Carbon\Carbon;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kasir = Pengguna::where('role', 'kasir')->first();
        if (!$kasir) return;

        $produk = Produk::all();
        if ($produk->isEmpty()) return;

        for ($i = 1; $i <= 5; $i++) {
            // Pilih 2 produk random untuk setiap pesanan
            $randomProducts = $produk->random(2);
            $totalHarga = 0;

            $pesanan = Pesanan::create([
                'kode_pesanan' => '#2026-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'id_pengguna' => $kasir->id,
                'no_meja' => 'Meja ' . rand(1, 15),
                'total_harga' => 0, // Akan diupdate nanti
                'metode_bayar' => ['tunai', 'qris', 'transfer'][array_rand(['tunai', 'qris', 'transfer'])],
                'status' => 'selesai',
                'tgl_pesanan' => Carbon::now()->subDays(rand(0, 5)),
            ]);

            foreach ($randomProducts as $p) {
                $jumlah = rand(1, 3);
                $subtotal = $p->harga * $jumlah;
                $totalHarga += $subtotal;

                ItemPesanan::create([
                    'id_pesanan' => $pesanan->id,
                    'id_produk' => $p->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $p->harga,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total harga
            $pesanan->update(['total_harga' => $totalHarga]);
        }
    }
}

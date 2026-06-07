<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = [
            [
                'nama_produk' => 'Espresso',
                'harga' => 15000,
                'kategori' => 'Kopi',
                'deskripsi' => 'Kopi hitam pekat dengan ekstraksi tinggi',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Americano',
                'harga' => 18000,
                'kategori' => 'Kopi',
                'deskripsi' => 'Espresso dengan tambahan air panas',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Cappuccino',
                'harga' => 22000,
                'kategori' => 'Kopi Susu',
                'deskripsi' => 'Espresso dengan susu steamed dan foam tebal',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Caffe Latte',
                'harga' => 22000,
                'kategori' => 'Kopi Susu',
                'deskripsi' => 'Espresso dengan susu steamed lebih banyak',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Kopi Susu Gula Aren',
                'harga' => 20000,
                'kategori' => 'Kopi Susu',
                'deskripsi' => 'Kopi susu kekinian dengan manisnya gula aren asli',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Mocha Latte',
                'harga' => 25000,
                'kategori' => 'Kopi Susu',
                'deskripsi' => 'Perpaduan espresso, susu, dan coklat',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Matcha Latte',
                'harga' => 24000,
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Minuman teh hijau Jepang dengan susu',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Red Velvet Latte',
                'harga' => 24000,
                'kategori' => 'Non-Kopi',
                'deskripsi' => 'Minuman manis rasa red velvet cake',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Lemon Tea',
                'harga' => 15000,
                'kategori' => 'Teh',
                'deskripsi' => 'Teh segar dengan perasan lemon',
                'tersedia' => true,
            ],
            [
                'nama_produk' => 'Kentang Goreng',
                'harga' => 15000,
                'kategori' => 'Camilan',
                'deskripsi' => 'Kentang goreng renyah disajikan dengan saus',
                'tersedia' => true,
            ],
        ];

        foreach ($produks as $produkData) {
            $kategori = \App\Models\Kategori::where('nama', $produkData['kategori'])->first();
            if ($kategori) {
                $produkData['id_kategori'] = $kategori->id;
            } else {
                $kategoriBaru = \App\Models\Kategori::create(['nama' => $produkData['kategori']]);
                $produkData['id_kategori'] = $kategoriBaru->id;
            }
            unset($produkData['kategori']);

            Produk::create($produkData);
        }
    }
}

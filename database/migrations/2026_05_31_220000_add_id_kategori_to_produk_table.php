<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom id_kategori sebagai nullable terlebih dahulu
        Schema::table('produk', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kategori')->nullable()->after('harga');
        });

        // 2. Migrasikan data dari kolom kategori (string) ke id_kategori (ID relasi)
        $produks = DB::table('produk')->get();
        foreach ($produks as $produk) {
            $kategoriNama = trim($produk->kategori);
            if (!empty($kategoriNama)) {
                // Cari apakah kategori dengan nama tersebut sudah ada di tabel kategori
                $kategori = DB::table('kategori')->where('nama', $kategoriNama)->first();
                
                if (!$kategori) {
                    // Jika belum ada, buat kategori baru otomatis demi keamanan data
                    $kategoriId = DB::table('kategori')->insertGetId([
                        'nama' => $kategoriNama,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $kategoriId = $kategori->id;
                }

                // Update produk dengan id_kategori baru
                DB::table('produk')->where('id', $produk->id)->update([
                    'id_kategori' => $kategoriId
                ]);
            }
        }

        // 3. Tambahkan constraint foreign key dan hapus kolom kategori lama
        Schema::table('produk', function (Blueprint $table) {
            // Karena data sudah dimigrasikan, kita ubah kolom id_kategori agar NOT NULL
            $table->unsignedBigInteger('id_kategori')->nullable(false)->change();
            
            $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('restrict');
            $table->dropColumn('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            // 1. Tambah kembali kolom kategori lama
            $table->string('kategori', 50)->nullable()->after('harga');
        });

        // 2. Kembalikan data nama dari tabel kategori ke kolom produk.kategori
        $produks = DB::table('produk')->get();
        foreach ($produks as $produk) {
            if ($produk->id_kategori) {
                $kategori = DB::table('kategori')->where('id', $produk->id_kategori)->first();
                if ($kategori) {
                    DB::table('produk')->where('id', $produk->id)->update([
                        'kategori' => $kategori->nama
                    ]);
                }
            }
        }

        // 3. Ubah kolom kategori kembali menjadi non-nullable, hapus foreign key dan id_kategori
        Schema::table('produk', function (Blueprint $table) {
            $table->string('kategori', 50)->nullable(false)->change();
            $table->dropForeign(['id_kategori']);
            $table->dropColumn('id_kategori');
        });
    }
};

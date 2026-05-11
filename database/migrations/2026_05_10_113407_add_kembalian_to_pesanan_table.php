<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom uang_diterima dan kembalian ke tabel pesanan.
     * Kedua kolom nullable karena hanya diisi untuk pembayaran tunai.
     */
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('uang_diterima', 10, 2)
                  ->nullable()
                  ->after('total_harga')
                  ->comment('Hanya diisi jika metode_bayar = tunai');

            $table->decimal('kembalian', 10, 2)
                  ->nullable()
                  ->after('uang_diterima')
                  ->comment('Hanya diisi jika metode_bayar = tunai. Nilai = uang_diterima - total_harga');
        });
    }

    /**
     * Rollback: hapus kedua kolom.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['uang_diterima', 'kembalian']);
        });
    }
};

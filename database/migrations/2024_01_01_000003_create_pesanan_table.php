<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 20)->unique();
            $table->unsignedBigInteger('id_pengguna');
            $table->string('no_meja', 100)->nullable();
            $table->decimal('total_harga', 10, 2);
            $table->enum('metode_bayar', ['tunai', 'qris', 'transfer']);
            $table->enum('status', ['proses', 'selesai', 'batal'])->default('proses');
            $table->timestamp('tgl_pesanan')->useCurrent();
            $table->timestamps();

            $table->foreign('id_pengguna')->references('id')->on('pengguna')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};

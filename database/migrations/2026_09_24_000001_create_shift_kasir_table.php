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
        // Membuat tabel shift_kasir untuk mencatat riwayat dan status shift kasir
        Schema::create('shift_kasir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pengguna'); // Relasi ke ID pengguna (kasir)
            $table->enum('nama_shift', ['pagi', 'sore', 'malam']); // Pilihan shift kerja
            $table->date('tgl_shift'); // Tanggal pelaksanaan shift
            $table->time('jam_mulai')->nullable(); // Waktu kasir memulai shift
            $table->time('jam_selesai')->nullable(); // Waktu kasir menyelesaikan shift
            $table->enum('status', ['aktif', 'selesai'])->default('aktif'); // Status shift
            $table->timestamps();

            // Definisi Foreign Key ke tabel pengguna dengan cascade delete
            $table->foreign('id_pengguna')
                  ->references('id')->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_kasir');
    }
};

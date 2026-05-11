<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'id_pengguna',
        'no_meja',
        'total_harga',
        'uang_diterima', // Ditambahkan: nominal tunai yang diterima kasir
        'kembalian',     // Ditambahkan: uang_diterima - total_harga (tunai saja)
        'metode_bayar',
        'status',
        'tgl_pesanan',
    ];

    protected $casts = [
        'tgl_pesanan'   => 'datetime',
        'uang_diterima' => 'decimal:2', // Ditambahkan: auto-cast ke desimal
        'kembalian'     => 'decimal:2', // Ditambahkan: auto-cast ke desimal
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'id_pesanan');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'harga',
        'id_kategori',
        'foto',
        'deskripsi',
        'tersedia',
        'stok',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'id_produk');
    }
}

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
        'kategori',
        'foto',
        'deskripsi',
        'tersedia',
    ];

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'id_produk');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftKasir extends Model
{
    use HasFactory;

    /**
     * Nama tabel terkait dalam database.
     *
     * @var string
     */
    protected $table = 'shift_kasir';

    /**
     * Atribut yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'nama_shift',
        'tgl_shift',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    /**
     * Relasi ke model Pengguna (kasir):
     * Setiap shift dimiliki oleh satu pengguna (kasir).
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}

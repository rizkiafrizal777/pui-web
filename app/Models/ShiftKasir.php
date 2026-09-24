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

    /**
     * Accessor untuk mendapatkan durasi kerja shift dalam format teks.
     */
    public function getDurasiAttribute()
    {
        if ($this->status === 'aktif' || empty($this->jam_selesai)) {
            return 'Sedang berjalan...';
        }

        if (empty($this->jam_mulai)) {
            return '-';
        }

        $mulai = \Carbon\Carbon::parse($this->jam_mulai);
        $selesai = \Carbon\Carbon::parse($this->jam_selesai);

        // Jika jam selesai lebih kecil dari jam mulai (shift malam melintasi tengah malam)
        if ($selesai->lessThan($mulai)) {
            $selesai = $selesai->copy()->addDay();
        }

        $totalMenit = $mulai->diffInMinutes($selesai);
        $jam = intdiv($totalMenit, 60);
        $menit = $totalMenit % 60;

        if ($jam > 0 && $menit > 0) {
            return "{$jam} jam {$menit} menit";
        } elseif ($jam > 0) {
            return "{$jam} jam";
        } else {
            return "{$menit} menit";
        }
    }
}

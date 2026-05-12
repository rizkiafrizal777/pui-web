<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Wajib untuk autentikasi Laravel
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Ditambahkan untuk autentikasi API (Sanctum)

class Pengguna extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pengguna'; // Mengarah ke tabel 'pengguna', bukan 'users'

    /**
     * Guard yang digunakan untuk autentikasi model ini.
     * Diset eksplisit agar tidak konflik jika ada guard lain.
     */
    protected $guard = 'web';

    /**
     * Atribut yang boleh diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role', // enum: 'admin' | 'kasir'
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi (misal: ke JSON/API).
     * Ditambahkan 'remember_token' agar tidak bocor ke response.
     */
    protected $hidden = [
        'password',
        'remember_token', // Ditambahkan: disembunyikan dari serialisasi
    ];

    /**
     * Relasi: satu pengguna bisa memiliki banyak pesanan.
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pengguna');
    }
}

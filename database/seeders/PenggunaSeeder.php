<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengguna::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@ngesis.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        Pengguna::create([
            'nama' => 'Kasir Satu',
            'email' => 'kasir@ngesis.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);
    }
}

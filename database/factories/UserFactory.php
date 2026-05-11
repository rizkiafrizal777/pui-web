<?php

namespace Database\Factories;

use App\Models\Pengguna; // Diubah: dari User::class ke Pengguna::class
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengguna>
 */
class UserFactory extends Factory
{
    /**
     * Model yang digunakan oleh factory ini.
     * Diubah: dari App\Models\User ke App\Models\Pengguna
     */
    protected $model = Pengguna::class;

    /**
     * Define the model's default state.
     * Field disesuaikan dengan kolom tabel 'pengguna'.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama'     => fake()->name(),                    // Diubah: 'name' → 'nama'
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),            // Diubah: pakai Hash::make, lebih aman
            'role'     => fake()->randomElement(['admin', 'kasir']), // Ditambahkan: sesuai enum kolom role
            // Dihapus: 'email_verified_at', 'remember_token' — tidak ada di tabel pengguna
        ];
    }

    /**
     * State untuk membuat pengguna dengan role admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * State untuk membuat pengguna dengan role kasir.
     */
    public function kasir(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'kasir',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class AuthController extends Controller
{
    /**
     * Proses login untuk API Mobile.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        // Verifikasi kredensial
        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Hapus token lama jika perlu (optional: agar tidak terlalu banyak token di DB)
        // $pengguna->tokens()->delete();

        // Buat token baru
        $token = $pengguna->createToken('ngesis_pos_mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => $pengguna,
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Proses logout untuk API Mobile.
     */
    public function logout(Request $request)
    {
        // Menghapus token yang sedang digunakan untuk mengakses API ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ], 200);
    }
}

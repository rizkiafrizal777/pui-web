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

    /**
     * Update email and/or password for the authenticated user.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => 'required|email|unique:pengguna,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'user' => $user
            ]
        ], 200);
    }
}

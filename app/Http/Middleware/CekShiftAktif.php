<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ShiftKasir;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CekShiftAktif
{
    /**
     * Periksa apakah kasir memiliki shift aktif hari ini sebelum
     * diizinkan mengakses halaman POS atau melakukan transaksi via API.
     *
     * - Web request  → redirect ke halaman shift dengan pesan peringatan
     * - API request  → return JSON 403
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Auth::id();

        // Cek apakah kasir memiliki shift aktif hari ini
        $shiftAktif = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', Carbon::today())
            ->exists(); // gunakan exists() agar lebih efisien (tidak fetch seluruh row)

        if (!$shiftAktif) {
            // Jika request berasal dari API (mobile) → kembalikan JSON error
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum memulai shift hari ini. Silakan mulai shift terlebih dahulu.',
                ], 403);
            }

            // Jika request berasal dari Web → redirect ke halaman shift dengan pesan
            return redirect()->route('kasir.shift')
                ->with('error', 'Anda harus memulai shift terlebih dahulu sebelum bisa menggunakan kasir.');
        }

        // Shift aktif → lanjutkan request
        return $next($request);
    }
}

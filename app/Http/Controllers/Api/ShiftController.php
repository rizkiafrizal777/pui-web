<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShiftKasir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Memeriksa status shift aktif milik kasir yang sedang login untuk hari ini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $userId = Auth::id() ?? $request->user()->id;

        // Cari shift aktif kasir hari ini
        $shiftAktif = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', Carbon::today())
            ->first();

        // Jika shift aktif ditemukan
        if ($shiftAktif) {
            return response()->json([
                'shift_aktif' => true,
                'data' => [
                    'id'          => $shiftAktif->id,
                    'nama_shift'  => $shiftAktif->nama_shift,
                    'tgl_shift'   => $shiftAktif->tgl_shift,
                    'jam_mulai'   => $shiftAktif->jam_mulai,
                    'status'      => $shiftAktif->status,
                ],
            ], 200);
        }

        // Jika tidak ada shift yang sedang aktif
        return response()->json([
            'shift_aktif' => false,
            'data'        => null,
        ], 200);
    }

    /**
     * Memulai shift baru untuk kasir.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mulai(Request $request)
    {
        // 1. Validasi input: nama_shift wajib dan nilainya harus pagi, sore, atau malam
        $request->validate([
            'nama_shift' => 'required|in:pagi,sore,malam',
        ], [
            'nama_shift.required' => 'Nama shift wajib diisi.',
            'nama_shift.in'       => 'Pilihan shift hanya boleh: pagi, sore, atau malam.',
        ]);

        $userId = Auth::id() ?? $request->user()->id;

        // 2. Cek apakah kasir sudah memiliki shift yang masih aktif hari ini
        $shiftAktif = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', Carbon::today())
            ->first();

        if ($shiftAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki shift aktif hari ini.',
            ], 400);
        }

        // 3. Simpan data shift baru ke database
        $shift = ShiftKasir::create([
            'id_pengguna' => $userId,
            'nama_shift'  => $request->nama_shift,
            'tgl_shift'   => Carbon::today()->toDateString(),
            'jam_mulai'   => Carbon::now()->format('H:i:s'),
            'status'      => 'aktif',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift ' . $shift->nama_shift . ' berhasil dimulai.',
            'data'    => $shift,
        ], 201);
    }

    /**
     * Mengakhiri shift aktif kasir untuk hari ini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function selesai(Request $request)
    {
        $userId = Auth::id() ?? $request->user()->id;

        // 1. Cari shift aktif milik kasir hari ini
        $shift = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', Carbon::today())
            ->first();

        // 2. Jika tidak ada shift aktif, kembalikan respons error
        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif yang ditemukan hari ini.',
            ], 404);
        }

        // 3. Update jam selesai dan ubah status menjadi 'selesai'
        $shift->update([
            'jam_selesai' => Carbon::now()->format('H:i:s'),
            'status'      => 'selesai',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift ' . $shift->nama_shift . ' berhasil diselesaikan.',
            'data'    => $shift,
        ], 200);
    }

    /**
     * Mengambil 20 riwayat shift terbaru milik kasir yang sedang login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function riwayat(Request $request)
    {
        $userId = Auth::id() ?? $request->user()->id;

        // Ambil 20 shift terbaru diurutkan dari tanggal terbaru dan ID terbaru
        $riwayat = ShiftKasir::where('id_pengguna', $userId)
            ->orderBy('tgl_shift', 'desc')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat shift kasir berhasil diambil.',
            'data'    => $riwayat,
        ], 200);
    }
}

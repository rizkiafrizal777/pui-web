<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShiftKasir;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftKasirController extends Controller
{
    /**
     * Halaman utama shift kasir:
     * - Jika sudah ada shift aktif → tampilkan status & tombol Selesai
     * - Jika belum ada shift → tampilkan form Mulai Shift
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id();
        $today  = Carbon::today();

        // Cari shift aktif kasir hari ini
        $shiftAktif = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', $today)
            ->first();

        // Ambil riwayat 5 shift terakhir kasir ini
        $riwayatShift = ShiftKasir::where('id_pengguna', $userId)
            ->orderBy('tgl_shift', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('pos.shift', compact('shiftAktif', 'riwayatShift'));
    }

    /**
     * Proses memulai shift baru:
     * - Cek apakah sudah ada shift aktif hari ini
     * - Jika belum → buat shift baru dengan status aktif
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mulai(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|in:pagi,sore,malam',
        ], [
            'nama_shift.required' => 'Pilih jenis shift terlebih dahulu.',
            'nama_shift.in'       => 'Pilihan shift tidak valid.',
        ]);

        $userId = Auth::id();
        $today  = Carbon::today();

        // Cek shift aktif yang sudah ada
        $shiftAktif = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', $today)
            ->first();

        if ($shiftAktif) {
            return redirect()->route('kasir.shift')
                ->with('error', 'Anda sudah memiliki shift aktif hari ini!');
        }

        // Buat shift baru
        ShiftKasir::create([
            'id_pengguna' => $userId,
            'nama_shift'  => $request->nama_shift,
            'tgl_shift'   => $today->toDateString(),
            'jam_mulai'   => Carbon::now()->format('H:i:s'),
            'status'      => 'aktif',
        ]);

        return redirect()->route('kasir.shift')
            ->with('success', 'Shift ' . ucfirst($request->nama_shift) . ' berhasil dimulai! Selamat bekerja.');
    }

    /**
     * Proses mengakhiri shift yang sedang aktif.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selesai()
    {
        $userId = Auth::id();

        // Cari shift aktif kasir hari ini
        $shift = ShiftKasir::where('id_pengguna', $userId)
            ->where('status', 'aktif')
            ->whereDate('tgl_shift', Carbon::today())
            ->first();

        if (!$shift) {
            return redirect()->route('kasir.shift')
                ->with('error', 'Tidak ada shift aktif yang dapat diselesaikan.');
        }

        // Update jam selesai dan status
        $shift->update([
            'jam_selesai' => Carbon::now()->format('H:i:s'),
            'status'      => 'selesai',
        ]);

        return redirect()->route('kasir.shift')
            ->with('success', 'Shift ' . ucfirst($shift->nama_shift) . ' telah diselesaikan. Terima kasih!');
    }
}

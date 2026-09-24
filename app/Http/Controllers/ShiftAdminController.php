<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShiftKasir;
use App\Models\Pengguna;
use Carbon\Carbon;

class ShiftAdminController extends Controller
{
    /**
     * Menampilkan halaman monitoring dan riwayat shift kasir di panel Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Ambil semua data shift hari ini dari seluruh kasir (relasi pengguna)
        // Urutkan status aktif terlebih dahulu, kemudian berdasarkan jam mulai terbaru
        $shiftHariIni = ShiftKasir::with('pengguna')
            ->whereDate('tgl_shift', $today)
            ->orderByRaw("FIELD(status, 'aktif', 'selesai')")
            ->orderBy('jam_mulai', 'desc')
            ->get();

        // 2. Ambil data riwayat shift 7 hari terakhir
        $tujuhHariLalu = Carbon::today()->subDays(6);
        $queryRiwayat = ShiftKasir::with('pengguna')
            ->whereDate('tgl_shift', '>=', $tujuhHariLalu);

        // Filter dropdown berdasarkan kasir tertentu jika dipilih oleh admin
        if ($request->filled('id_pengguna')) {
            $queryRiwayat->where('id_pengguna', $request->id_pengguna);
        }

        // Paginasi 10 record per halaman dengan mempertahankan query string filter
        $shiftRiwayat = $queryRiwayat->orderBy('tgl_shift', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Ambil daftar pengguna dengan role kasir untuk pilihan di filter dropdown
        $daftarKasir = Pengguna::where('role', 'kasir')
            ->orderBy('nama', 'asc')
            ->get();

        // Kirim variabel ke view admin.shift.index
        return view('admin.shift.index', compact('shiftHariIni', 'shiftRiwayat', 'daftarKasir'));
    }
}

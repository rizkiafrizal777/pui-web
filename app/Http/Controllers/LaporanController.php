<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenjualanExport;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan dengan filter tanggal, kasir, dan shift.
     */
    public function index(Request $request)
    {
        // 1. Ambil parameter filter
        $dari = $request->input('dari', Carbon::today()->format('Y-m-d'));
        $sampai = $request->input('sampai', Carbon::today()->format('Y-m-d'));
        $kasirId = $request->kasir_id;
        $namaShift = $request->nama_shift;

        // Ubah jadi objek Carbon untuk query rentang waktu satu hari penuh
        $start = Carbon::parse($dari)->startOfDay();
        $end = Carbon::parse($sampai)->endOfDay();

        // 2. Query data pesanan sesuai filter untuk tabel (dengan paginasi)
        $query = Pesanan::with(['pengguna', 'itemPesanan.produk'])
            ->whereBetween('tgl_pesanan', [$start, $end]);

        // Filter kasir jika dipilih
        if ($kasirId) {
            $query->where('id_pengguna', $kasirId);
        }

        // Filter shift jika dipilih (berdasarkan riwayat shift kasir pada rentang tanggal)
        if ($namaShift) {
            $query->whereHas('pengguna.shiftKasir', function($q) use ($namaShift, $dari, $sampai) {
                $q->where('nama_shift', $namaShift)
                  ->whereBetween('tgl_shift', [$dari, $sampai]);
            });
        }

        $pesanans = $query->orderBy('tgl_pesanan', 'desc')->paginate(15);
            
        // Append semua query parameter ke pagination links
        $pesanans->appends([
            'dari' => $dari,
            'sampai' => $sampai,
            'kasir_id' => $kasirId,
            'nama_shift' => $namaShift,
        ]);

        // 3. Query untuk Summary Cards (Hanya pesanan berstatus 'selesai')
        $pesananSelesai = Pesanan::whereBetween('tgl_pesanan', [$start, $end])
            ->where('status', 'selesai');

        if ($kasirId) {
            $pesananSelesai->where('id_pengguna', $kasirId);
        }

        if ($namaShift) {
            $pesananSelesai->whereHas('pengguna.shiftKasir', function($q) use ($namaShift, $dari, $sampai) {
                $q->where('nama_shift', $namaShift)
                  ->whereBetween('tgl_shift', [$dari, $sampai]);
            });
        }
            
        $totalTransaksi = $pesananSelesai->count();
        $totalPendapatan = $pesananSelesai->sum('total_harga');
        
        $rataRata = 0;
        if ($totalTransaksi > 0) {
            $rataRata = $totalPendapatan / $totalTransaksi;
        }

        // 4. Produk Terlaris di periode & filter tersebut
        $produkTerlarisQuery = ItemPesanan::selectRaw('id_produk, SUM(jumlah) as total')
            ->whereIn('id_pesanan', (clone $pesananSelesai)->select('id'))
            ->groupBy('id_produk')
            ->orderByDesc('total')
            ->with('produk')
            ->first();

        $produkTerlaris = '-';
        if ($produkTerlarisQuery && $produkTerlarisQuery->produk) {
            $produkTerlaris = $produkTerlarisQuery->produk->nama_produk . ' (' . $produkTerlarisQuery->total . ' cup)';
        }

        // 5. Grand Total (dari transaksi selesai sesuai filter)
        $grandTotal = $totalPendapatan;

        // 6. Data Kasir untuk Dropdown & Nama Kasir Aktif
        $kasirs = Pengguna::where('role', 'kasir')->get();
        $namaKasirAktif = $kasirId ? Pengguna::find($kasirId)?->nama : null;

        // 7. Statistik Performa Per Kasir
        $performaKasir = Pengguna::where('role', 'kasir')
            ->withCount(['pesanan as total_transaksi' => fn($q) =>
                $q->whereBetween('tgl_pesanan', [$start, $end])
                  ->where('status', 'selesai')])
            ->withSum(['pesanan as total_pendapatan' => fn($q) =>
                $q->whereBetween('tgl_pesanan', [$start, $end])
                  ->where('status', 'selesai')], 'total_harga')
            ->get()
            ->sortByDesc('total_pendapatan');

        return view('admin.laporan.index', compact(
            'pesanans',
            'dari',
            'sampai',
            'kasirId',
            'namaShift',
            'kasirs',
            'namaKasirAktif',
            'performaKasir',
            'totalTransaksi',
            'totalPendapatan',
            'rataRata',
            'produkTerlaris',
            'grandTotal'
        ));
    }

    /**
     * Export laporan ke file Excel dengan filter yang aktif.
     */
    public function export(Request $request)
    {
        $dari = $request->input('dari', Carbon::today()->format('Y-m-d'));
        $sampai = $request->input('sampai', Carbon::today()->format('Y-m-d'));
        $kasirId = $request->input('kasir_id');
        $namaShift = $request->input('nama_shift');

        $namaKasirAktif = $kasirId ? Pengguna::find($kasirId)?->nama : null;

        return Excel::download(
            new LaporanPenjualanExport(
                $dari, $sampai, $kasirId, $namaShift
            ),
            'laporan-'
            . ($namaKasirAktif ? Str::slug($namaKasirAktif) : 'semua')
            . '-' . $dari . '-sd-' . $sampai . '.xlsx'
        );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenjualanExport;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan.
     */
    public function index(Request $request)
    {
        // Set default filter: Hari ini
        $dari = $request->input('dari', Carbon::today()->format('Y-m-d'));
        $sampai = $request->input('sampai', Carbon::today()->format('Y-m-d'));

        // Ubah jadi objek Carbon untuk query
        $start = Carbon::parse($dari)->startOfDay();
        $end = Carbon::parse($sampai)->endOfDay();

        // 1. Ambil data pesanan sesuai range tanggal untuk tabel (dengan paginasi)
        $pesanans = Pesanan::with(['pengguna', 'itemPesanan.produk'])
            ->whereBetween('tgl_pesanan', [$start, $end])
            ->orderBy('tgl_pesanan', 'desc')
            ->paginate(15);
            
        // Append query parameter ke link pagination
        $pesanans->appends(['dari' => $dari, 'sampai' => $sampai]);

        // 2. Query untuk Summary Cards (Hanya yang Selesai)
        $pesananSelesai = Pesanan::whereBetween('tgl_pesanan', [$start, $end])
            ->where('status', 'selesai');
            
        $totalTransaksi = $pesananSelesai->count();
        $totalPendapatan = $pesananSelesai->sum('total_harga');
        
        $rataRata = 0;
        if ($totalTransaksi > 0) {
            $rataRata = $totalPendapatan / $totalTransaksi;
        }

        // 3. Produk Terlaris di periode tersebut
        // Menggunakan JOIN untuk memastikan hanya mengambil item dari pesanan yang 'selesai' dan dalam rentang tanggal
        $produkTerlarisQuery = ItemPesanan::selectRaw('item_pesanan.id_produk, SUM(item_pesanan.jumlah) as total')
            ->join('pesanan', 'item_pesanan.id_pesanan', '=', 'pesanan.id')
            ->whereBetween('pesanan.tgl_pesanan', [$start, $end])
            ->where('pesanan.status', 'selesai')
            ->groupBy('item_pesanan.id_produk')
            ->orderByDesc('total')
            ->with('produk') // Load relasi produk untuk mengambil nama_produk
            ->first();

        $produkTerlaris = '-';
        if ($produkTerlarisQuery && $produkTerlarisQuery->produk) {
            $produkTerlaris = $produkTerlarisQuery->produk->nama_produk . ' (' . $produkTerlarisQuery->total . ' cup)';
        }

        // 4. Hitung Grand Total dari filter saat ini (untuk footer tabel)
        // Jika status yang dihitung di footer adalah semua yang ditampilkan (termasuk proses/batal), atau hanya yang selesai?
        // Sesuai konvensi, biasanya yang sudah dibayar (selesai)
        $grandTotal = $totalPendapatan;

        return view('admin.laporan.index', compact(
            'pesanans', 'dari', 'sampai', 'totalTransaksi', 'totalPendapatan', 'rataRata', 'produkTerlaris', 'grandTotal'
        ));
    }

    /**
     * Export laporan ke file Excel.
     */
    public function export(Request $request)
    {
        $dari = $request->input('dari', Carbon::today()->format('Y-m-d'));
        $sampai = $request->input('sampai', Carbon::today()->format('Y-m-d'));

        $namaFile = 'laporan-penjualan-' . $dari . '-sd-' . $sampai . '.xlsx';

        return Excel::download(new LaporanPenjualanExport($dari, $sampai), $namaFile);
    }
}

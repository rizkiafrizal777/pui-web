<?php

namespace App\Exports;

use App\Models\Pesanan;
use App\Models\Pengguna;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $dari;
    protected $sampai;
    protected $kasirId;
    protected $namaShift;
    protected $namaKasir;
    protected $totalPendapatan = 0;
    protected $barisTerakhir = 0;

    /**
     * Constructor dengan parameter tanggal, kasir_id, dan nama_shift.
     */
    public function __construct($dari, $sampai, $kasirId = null, $namaShift = null)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
        $this->kasirId = $kasirId;
        $this->namaShift = $namaShift;

        // Ambil nama kasir jika kasirId ditentukan
        if ($this->kasirId) {
            $this->namaKasir = Pengguna::find($this->kasirId)?->nama ?? 'Semua Kasir';
        } else {
            $this->namaKasir = 'Semua Kasir';
        }
    }

    /**
     * Ambil data pesanan sesuai filter.
     */
    public function collection()
    {
        $start = Carbon::parse($this->dari)->startOfDay();
        $end = Carbon::parse($this->sampai)->endOfDay();

        $query = Pesanan::with(['pengguna', 'itemPesanan'])
            ->whereBetween('tgl_pesanan', [$start, $end]);

        // Filter kasir jika ada
        if ($this->kasirId) {
            $query->where('id_pengguna', $this->kasirId);
        }

        // Filter shift jika ada
        if ($this->namaShift) {
            $query->whereHas('pengguna.shiftKasir', function($q) {
                $q->where('nama_shift', $this->namaShift)
                  ->whereBetween('tgl_shift', [$this->dari, $this->sampai]);
            });
        }

        $pesanans = $query->orderBy('tgl_pesanan', 'asc')->get();

        // Hitung total pendapatan untuk baris terakhir (hanya status 'selesai')
        $this->totalPendapatan = $pesanans->where('status', 'selesai')->sum('total_harga');
        
        // Baris terakhir: 5 baris headings + count($pesanans) data + 1 baris footer
        $this->barisTerakhir = count($pesanans) + 6; 

        $data = $pesanans->toArray();
        
        // Tambahkan baris footer total pendapatan
        $data[] = [
            'is_footer' => true,
            'total_pendapatan' => $this->totalPendapatan
        ];

        return collect($data);
    }

    /**
     * Map data untuk setiap baris di spreadsheet.
     */
    public function map($pesanan): array
    {
        // Jika baris footer
        if (isset($pesanan['is_footer'])) {
            return [
                '', '', '', '', 
                'TOTAL PENDAPATAN', 
                $pesanan['total_pendapatan'], 
                '', ''
            ];
        }

        // Hitung total item dalam satu pesanan
        $totalItem = 0;
        if (isset($pesanan['item_pesanan']) && is_array($pesanan['item_pesanan'])) {
            foreach ($pesanan['item_pesanan'] as $item) {
                $totalItem += $item['jumlah'] ?? 0;
            }
        }

        return [
            $pesanan['kode_pesanan'],
            Carbon::parse($pesanan['tgl_pesanan'])->format('d-m-Y H:i'),
            $pesanan['no_meja'] ?? '-',
            $pesanan['pengguna']['nama'] ?? '-',
            $totalItem,
            $pesanan['total_harga'],
            strtoupper($pesanan['metode_bayar'] ?? '-'),
            strtoupper($pesanan['status'] ?? '-'),
        ];
    }

    /**
     * Header sheet dan header kolom tabel.
     */
    public function headings(): array
    {
        return [
            ['Laporan Penjualan Ngesis Coffee'],
            ['Periode  : ' . $this->dari . ' s/d ' . $this->sampai],
            ['Kasir    : ' . ($this->namaKasir ?? 'Semua Kasir')],
            ['Shift    : ' . ($this->namaShift ? ucfirst($this->namaShift) : 'Semua Shift')],
            [
                'Kode Pesanan',
                'Tanggal',
                'No. Meja',
                'Kasir',
                'Total Item',
                'Total Harga',
                'Metode Bayar',
                'Status'
            ]
        ];
    }

    /**
     * Styling Excel.
     */
    public function styles(Worksheet $sheet)
    {
        // Merge cell untuk baris header info
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        $sheet->mergeCells('A4:H4');
        
        // Style judul utama
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Style info laporan
        $sheet->getStyle('A2:A4')->getFont()->setBold(true);

        // Style header tabel (Baris ke-5)
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9D9D9');

        // Style baris total di baris terakhir (Bold & background kuning)
        $barisTotal = $this->barisTerakhir;
        $sheet->getStyle('A' . $barisTotal . ':H' . $barisTotal)->getFont()->setBold(true);
        $sheet->getStyle('A' . $barisTotal . ':H' . $barisTotal)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF00'); // Background kuning

        return [];
    }

    /**
     * Judul Sheet.
     */
    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}

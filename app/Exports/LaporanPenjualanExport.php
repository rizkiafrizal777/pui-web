<?php

namespace App\Exports;

use App\Models\Pesanan;
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
    protected $totalPendapatan = 0;
    protected $barisTerakhir = 0;

    public function __construct($dari, $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function collection()
    {
        $start = Carbon::parse($this->dari)->startOfDay();
        $end = Carbon::parse($this->sampai)->endOfDay();

        $pesanans = Pesanan::with(['pengguna', 'itemPesanan'])
            ->whereBetween('tgl_pesanan', [$start, $end])
            ->orderBy('tgl_pesanan', 'asc')
            ->get();

        // Hitung total pendapatan untuk baris terakhir (hanya yang selesai)
        $this->totalPendapatan = $pesanans->where('status', 'selesai')->sum('total_harga');
        
        // Simpan posisi baris terakhir (Headings = 2 baris, plus jumlah data)
        // Header title di baris 1, Header tabel di baris 2
        $this->barisTerakhir = count($pesanans) + 2; 

        // Untuk menambahkan baris Total di bawah, kita append collection kosong lalu di mapping kita handle.
        // Tapi cara lebih clean adalah membiarkan array collection.
        // Excel-maatwebsite mendukung mengembalikan Array.
        $data = $pesanans->toArray();
        
        // Menambahkan baris extra untuk footer Total
        $data[] = [
            'is_footer' => true,
            'total_pendapatan' => $this->totalPendapatan
        ];

        return collect($data);
    }

    /**
     * Map data untuk setiap baris.
     */
    public function map($pesanan): array
    {
        // Jika ini adalah baris footer
        if (isset($pesanan['is_footer'])) {
            return [
                '', '', '', '', '', 
                'TOTAL PENDAPATAN', 
                $pesanan['total_pendapatan'], 
                '', ''
            ];
        }

        // Hitung total item pesanan
        $totalItem = 0;
        foreach ($pesanan['item_pesanan'] as $item) {
            $totalItem += $item['jumlah'];
        }

        return [
            $pesanan['kode_pesanan'],
            Carbon::parse($pesanan['tgl_pesanan'])->format('d-m-Y H:i'),
            $pesanan['no_meja'] ?? '-',
            $pesanan['pengguna']['nama'] ?? '-',
            $totalItem,
            $pesanan['total_harga'],
            strtoupper($pesanan['metode_bayar']),
            strtoupper($pesanan['status']),
        ];
    }

    /**
     * Header tabel.
     */
    public function headings(): array
    {
        return [
            ['Laporan Penjualan Ngesis Coffee - Periode: ' . $this->dari . ' s/d ' . $this->sampai],
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
        // Merge cell untuk judul laporan
        $sheet->mergeCells('A1:H1');
        
        // Style judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Style header tabel
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9D9D9');

        // Style baris total
        $barisTotal = $this->barisTerakhir + 1; // +1 karena index cell mulai dari 1, dan tambah baris footer
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan {{ $pesanan->kode_pesanan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: 'Courier New', Courier, monospace;
        }
        .struk-container {
            width: 80mm;
            margin: 20px auto;
            background-color: #fff;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            font-size: 12px;
        }
        td { padding: 2px 0; vertical-align: top;}
        .col-qty { width: 15%; text-align: center; }
        .col-price { width: 30%; text-align: right; }
        .col-sub { width: 30%; text-align: right; }
        
        .no-print-area {
            text-align: center;
            margin: 20px;
        }

        @media print {
            body {
                background-color: transparent;
                margin: 0;
                padding: 0;
            }
            .no-print-area {
                display: none;
            }
            .struk-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            /* Menghilangkan margin browser bawaan */
            @page {
                margin: 0; 
            }
        }
    </style>
</head>
<body>

    <div class="no-print-area">
        <button class="btn btn-secondary me-2" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak Struk
        </button>
        <a href="{{ route('kasir.pos') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Pesanan Baru
        </a>
    </div>

    <div class="struk-container">
        <div class="text-center">
            <h4 class="mb-0 fw-bold">NGESIS COFFEE</h4>
        </div>

        <div class="dashed-line"></div>

        <div style="font-size: 12px;">
            <table style="width: 100%;">
                <tr>
                    <td>No.</td>
                    <td>: {{ $pesanan->kode_pesanan }}</td>
                </tr>
                <tr>
                    <td>Tgl</td>
                    <td>: {{ $pesanan->tgl_pesanan->format('d-m-Y H:i') }}</td>
                </tr>

                @if($pesanan->no_meja)
                <tr>
                    <td>No. Meja</td>
                    <td>: {{ $pesanan->no_meja }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="dashed-line"></div>

        <table>
            @foreach($pesanan->itemPesanan as $item)
            <tr>
                <td>{{ $item->produk->nama_produk ?? 'Produk Dihapus' }}</td>
                <td class="text-right fw-bold">{{ $item->jumlah }}x</td>
            </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <table>
            <tr>
                <td class="fw-bold">Total Produk Dipesan</td>
                <td class="fw-bold text-right">{{ $pesanan->itemPesanan->sum('jumlah') }} item</td>
            </tr>
            <tr>
                <td>Status Pesanan</td>
                <td class="text-right">{{ strtoupper($pesanan->status) }}</td>
            </tr>
        </table>



    </div>
</body>
</html>

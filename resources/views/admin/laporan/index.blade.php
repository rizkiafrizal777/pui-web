@extends('layouts.app')

@section('title', 'Laporan Penjualan - Ngesis Coffee')
@section('header_title', 'Laporan Penjualan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body bg-white rounded">
                <form action="{{ route('laporan.index') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label for="dari" class="form-label text-muted small fw-bold">Dari Tanggal</label>
                        <input type="date" class="form-control" id="dari" name="dari" value="{{ $dari }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="sampai" class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="sampai" name="sampai" value="{{ $sampai }}" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="background-color: #6F4E37; border-color: #6F4E37;">
                            <i class="bi bi-filter"></i> Tampilkan
                        </button>
                        <a href="{{ route('laporan.export', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm h-100" style="background-color: #4b3621 !important;">
            <div class="card-body">
                <h6 class="card-title text-white-50">Total Transaksi</h6>
                <h3 class="mb-0">{{ number_format($totalTransaksi, 0, ',', '.') }} <small class="fs-6 fw-normal text-white-50">pesanan</small></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Total Pendapatan</h6>
                <h3 class="mb-0"><small class="fs-6 fw-normal text-white-50">Rp</small> {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Rata-rata Transaksi</h6>
                <h3 class="mb-0"><small class="fs-6 fw-normal text-white-50">Rp</small> {{ number_format($rataRata, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning text-dark shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-dark-50">Produk Terlaris</h6>
                <h5 class="mb-0 fw-bold mt-2">{{ $produkTerlaris }}</h5>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Data -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Kode Pesanan</th>
                        <th>Tanggal</th>
                        <th>No. Meja</th>
                        <th>Kasir</th>
                        <th class="text-center">Item</th>
                        <th class="text-end">Total Harga</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $index => $p)
                    <tr>
                        <td class="ps-4">{{ $pesanans->firstItem() + $index }}</td>
                        <td class="fw-bold">{{ $p->kode_pesanan }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tgl_pesanan)->format('d-m-Y H:i') }}</td>
                        <td>{{ $p->no_meja ?? '-' }}</td>
                        <td>{{ $p->pengguna->nama ?? '-' }}</td>
                        <td class="text-center">
                            {{ $p->itemPesanan->sum('jumlah') }}
                        </td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($p->metode_bayar) }}</span></td>
                        <td>
                            @if($p->status == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($p->status == 'proses')
                                <span class="badge bg-warning text-dark">Proses</span>
                            @else
                                <span class="badge bg-danger">Batal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                            Tidak ada data pesanan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end pe-3">GRAND TOTAL (Selesai):</td>
                        <td class="text-end text-success fs-5">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-end mb-0">
            {{ $pesanans->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

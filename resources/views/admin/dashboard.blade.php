@extends('layouts.app')

@section('title', 'Dashboard Admin - Ngesis Coffee')
@section('header_title', 'Dashboard Utama')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold text-secondary">Selamat Datang, {{ Auth::user()->nama }}</h4>
        <p class="text-muted">Berikut adalah ringkasan performa Ngesis Coffee hari ini.</p>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row g-4 mb-4">
    <!-- Pendapatan Hari Ini -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #6F4E37, #4b3621); color: white;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-normal text-white-50">Pendapatan Hari Ini</h6>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Pesanan Hari Ini -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-success border-5">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-muted">Pesanan Hari Ini</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-cart-check fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $pesananHariIni }} <span class="fs-6 fw-normal text-muted">transaksi</span></h3>
            </div>
        </div>
    </div>

    <!-- Total Produk -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-5">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-muted">Total Produk Aktif</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalProduk }} <span class="fs-6 fw-normal text-muted">item</span></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tabel Pesanan Terbaru -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Terbaru</h6>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kode</th>
                                <th>Waktu</th>
                                <th>Kasir</th>
                                <th class="text-end">Total</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesananTerbaru as $pesanan)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $pesanan->kode_pesanan }}</td>
                                <td>{{ \Carbon\Carbon::parse($pesanan->tgl_pesanan)->diffForHumans() }}</td>
                                <td>{{ $pesanan->pengguna->nama ?? '-' }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center pe-4">
                                    @if($pesanan->status == 'selesai')
                                        <span class="badge bg-success rounded-pill px-3">Selesai</span>
                                    @elseif($pesanan->status == 'proses')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Proses</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3">Batal</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi terbaru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge me-2"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    <a href="{{ route('produk.create') }}" class="btn btn-lg text-start d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                        <span><i class="bi bi-plus-circle text-primary me-2"></i> Tambah Produk</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('admin.pengguna.create') }}" class="btn btn-lg text-start d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                        <span><i class="bi bi-person-plus text-info me-2"></i> Tambah Pengguna</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="btn btn-lg text-start d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                        <span><i class="bi bi-printer text-success me-2"></i> Cetak Laporan</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-lg text-start d-flex justify-content-between align-items-center" style="background-color: #fff5f5; border: 1px solid #ffcaca; color: #dc3545;">
                            <span><i class="bi bi-box-arrow-right me-2"></i> Logout Aplikasi</span>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

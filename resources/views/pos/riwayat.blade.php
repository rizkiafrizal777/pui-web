@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Ngesis Coffee')
@section('header_title', 'Riwayat Pesanan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Kode Pesanan</th>
                                <th>Waktu Transaksi</th>
                                <th>No. Meja</th>
                                <th class="text-center">Total Item</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanans as $index => $p)
                            <tr>
                                <td class="ps-4">{{ $pesanans->firstItem() + $index }}</td>
                                <td class="fw-bold text-secondary">{{ $p->kode_pesanan }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($p->tgl_pesanan)->format('d M Y, H:i') }}
                                    <small class="d-block text-muted">{{ \Carbon\Carbon::parse($p->tgl_pesanan)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($p->no_meja)
                                        <span class="badge bg-light text-dark border">{{ $p->no_meja }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $p->itemPesanan->sum('jumlah') }} pcs
                                </td>
                                <td class="text-center">
                                    @if($p->status == 'selesai')
                                        <span class="badge bg-success rounded-pill px-3">Selesai</span>
                                    @elseif($p->status == 'proses')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Proses</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3">Batal</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('pos.struk', $p->id) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Lihat/Cetak Tiket Pesanan">
                                        <i class="bi bi-printer"></i> Cetak
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                    Belum ada riwayat pesanan yang Anda buat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pesanans->hasPages())
            <div class="card-footer bg-white py-3 border-top-0">
                <div class="d-flex justify-content-end mb-0">
                    {{ $pesanans->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

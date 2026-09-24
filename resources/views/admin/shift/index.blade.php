@extends('layouts.app')

@section('title', 'Manajemen Shift Kasir - Ngesis Coffee')
@section('header_title', 'Manajemen Shift Kasir')

@section('content')
<div class="container-fluid px-0">

    <!-- Header & Keterangan Halaman -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-secondary mb-1">
                    <i class="bi bi-calendar-check text-warning me-2"></i>Monitoring Shift Kasir
                </h4>
                <p class="text-muted mb-0">Pantau shift aktif kasir hari ini serta tinjau riwayat kerja 7 hari terakhir.</p>
            </div>
            <div>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-calendar3 me-1 text-primary"></i> {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="bi bi-person-workspace fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Shift Aktif Saat Ini</span>
                        <h3 class="fw-bold mb-0 text-success">
                            {{ $shiftHariIni->where('status', 'aktif')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Total Shift Hari Ini</span>
                        <h3 class="fw-bold mb-0 text-primary">{{ $shiftHariIni->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #6c757d !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3 text-secondary">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Shift Selesai Hari Ini</span>
                        <h3 class="fw-bold mb-0 text-secondary">
                            {{ $shiftHariIni->where('status', 'selesai')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 1 — Shift Aktif Hari Ini -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-secondary mb-0">
                <i class="bi bi-play-circle text-success me-2"></i>Shift Aktif Hari Ini
            </h5>
            <span class="badge bg-light text-muted border px-2 py-1">Hari Ini</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 60px;">No</th>
                            <th>Nama Kasir</th>
                            <th>Shift</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th class="pe-4">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shiftHariIni as $index => $shift)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light border text-secondary d-flex align-items-center justify-content-center me-2" style="width: 34px; height: 34px; font-weight: 600;">
                                        {{ strtoupper(substr($shift->pengguna->nama ?? 'K', 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $shift->pengguna->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                {{-- Badge Shift: Pagi, Sore, Malam --}}
                                @if(strtolower($shift->nama_shift) === 'pagi')
                                    <span class="badge" style="background-color: #fef08a; color: #854d0e; font-weight: 600; padding: 6px 12px; border-radius: 20px;">
                                        <i class="bi bi-sun me-1"></i>Pagi
                                    </span>
                                @elseif(strtolower($shift->nama_shift) === 'sore')
                                    <span class="badge" style="background-color: #e0f2fe; color: #0369a1; font-weight: 600; padding: 6px 12px; border-radius: 20px;">
                                        <i class="bi bi-cloud-sun me-1"></i>Sore
                                    </span>
                                @elseif(strtolower($shift->nama_shift) === 'malam')
                                    <span class="badge" style="background-color: #f3e8ff; color: #6b21a8; font-weight: 600; padding: 6px 12px; border-radius: 20px;">
                                        <i class="bi bi-moon-stars me-1"></i>Malam
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3">{{ ucfirst($shift->nama_shift) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-secondary">
                                    <i class="bi bi-clock me-1 text-muted"></i>{{ $shift->jam_mulai ? \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-secondary">
                                    <i class="bi bi-clock me-1 text-muted"></i>{{ $shift->jam_selesai ? \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') : '-' }}
                                </span>
                            </td>
                            <td>
                                {{-- Badge Status: Aktif / Selesai --}}
                                @if($shift->status === 'aktif')
                                    <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                                        <i class="bi bi-record-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4">
                                {{-- Kolom Durasi --}}
                                @if($shift->status === 'aktif')
                                    <span class="text-primary fw-semibold d-inline-flex align-items-center">
                                        <span class="spinner-grow spinner-grow-sm text-primary me-2" role="status" style="width: 0.6rem; height: 0.6rem;"></span>
                                        Sedang berjalan...
                                    </span>
                                @else
                                    <span class="text-dark fw-semibold">
                                        <i class="bi bi-hourglass-split me-1 text-muted"></i>{{ $shift->durasi }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <div class="py-3">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">Belum ada aktivitas shift kasir hari ini.</p>
                                    <small class="text-muted">Data shift kasir akan tampil otomatis saat kasir memulai shift dari aplikasi POS.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2 — Riwayat Shift 7 Hari Terakhir -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold text-secondary mb-1">
                        <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Shift
                    </h5>
                    <small class="text-muted">Menampilkan catatan shift kerja kasir selama 7 hari terakhir.</small>
                </div>

                <!-- Filter Dropdown by Kasir (Opsional) -->
                <div>
                    <form action="{{ route('admin.shift') }}" method="GET" class="d-flex align-items-center gap-2">
                        <label for="id_pengguna" class="form-label text-muted small fw-semibold mb-0 text-nowrap">
                            <i class="bi bi-funnel me-1"></i>Kasir:
                        </label>
                        <select name="id_pengguna" id="id_pengguna" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                            <option value="">Semua Kasir</option>
                            @foreach($daftarKasir as $kasir)
                                <option value="{{ $kasir->id }}" {{ request('id_pengguna') == $kasir->id ? 'selected' : '' }}>
                                    {{ $kasir->nama }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('id_pengguna'))
                            <a href="{{ route('admin.shift') }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Reset filter">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 60px;">No</th>
                            <th>Tanggal</th>
                            <th>Nama Kasir</th>
                            <th>Shift</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th class="pe-4">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shiftRiwayat as $index => $riwayat)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">
                                {{ $shiftRiwayat->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>{{ \Carbon\Carbon::parse($riwayat->tgl_shift)->translatedFormat('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-secondary">{{ $riwayat->pengguna->nama ?? '-' }}</span>
                            </td>
                            <td>
                                {{-- Badge Shift --}}
                                @if(strtolower($riwayat->nama_shift) === 'pagi')
                                    <span class="badge" style="background-color: #fef08a; color: #854d0e; font-weight: 600; padding: 5px 10px; border-radius: 20px;">
                                        Pagi
                                    </span>
                                @elseif(strtolower($riwayat->nama_shift) === 'sore')
                                    <span class="badge" style="background-color: #e0f2fe; color: #0369a1; font-weight: 600; padding: 5px 10px; border-radius: 20px;">
                                        Sore
                                    </span>
                                @elseif(strtolower($riwayat->nama_shift) === 'malam')
                                    <span class="badge" style="background-color: #f3e8ff; color: #6b21a8; font-weight: 600; padding: 5px 10px; border-radius: 20px;">
                                        Malam
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3">{{ ucfirst($riwayat->nama_shift) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary">
                                    {{ $riwayat->jam_mulai ? \Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-secondary">
                                    {{ $riwayat->jam_selesai ? \Carbon\Carbon::parse($riwayat->jam_selesai)->format('H:i') : '-' }}
                                </span>
                            </td>
                            <td class="pe-4">
                                @if($riwayat->status === 'aktif')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                        Sedang berjalan...
                                    </span>
                                @else
                                    <span class="fw-semibold text-secondary">
                                        {{ $riwayat->durasi }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Tidak ada riwayat shift dalam 7 hari terakhir.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginasi 10 per halaman -->
            @if($shiftRiwayat->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <small class="text-muted">
                    Menampilkan {{ $shiftRiwayat->firstItem() ?? 0 }} - {{ $shiftRiwayat->lastItem() ?? 0 }} dari total {{ $shiftRiwayat->total() }} shift
                </small>
                <div>
                    {{ $shiftRiwayat->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

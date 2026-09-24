@extends('layouts.app')

@section('title', 'Shift Kasir - Ngesis Coffee')
@section('header_title', 'Shift Kasir')

@section('content')
<div class="container-fluid px-0">

    {{-- ─── Alert Success / Error ─── --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ─── Salam & Info Tanggal ─── --}}
    <div class="row mb-4 align-items-center">
        <div class="col">
            @php
                $jam = \Carbon\Carbon::now()->hour;
                if ($jam >= 5  && $jam < 12) { $salam = 'Selamat Pagi'; $icon = 'bi-sun'; }
                elseif ($jam >= 12 && $jam < 17) { $salam = 'Selamat Siang'; $icon = 'bi-cloud-sun'; }
                elseif ($jam >= 17 && $jam < 20) { $salam = 'Selamat Sore'; $icon = 'bi-sunset'; }
                else { $salam = 'Selamat Malam'; $icon = 'bi-moon-stars'; }
            @endphp
            <h4 class="fw-bold text-secondary mb-1">
                <i class="bi {{ $icon }} text-warning me-2"></i>{{ $salam }}, {{ Auth::user()->nama }}!
            </h4>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                &nbsp;|&nbsp;
                <i class="bi bi-clock me-1"></i><span id="jam-sekarang">{{ \Carbon\Carbon::now()->format('H:i') }}</span>
            </p>
        </div>
    </div>

    <div class="row g-4">
        {{-- ─── KIRI: Status / Form Shift ─── --}}
        <div class="col-lg-7">
            @if($shiftAktif)
                {{-- ═══ Kondisi: Shift Sedang Aktif ═══ --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    {{-- Strip warna atas --}}
                    <div style="height: 6px; background: linear-gradient(90deg, #198754, #20c997);"></div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="bi bi-person-workspace text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-success">Shift Sedang Berjalan</h5>
                                <small class="text-muted">Shift Anda aktif hari ini</small>
                            </div>
                            {{-- Indikator live --}}
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <span class="badge bg-success px-3 py-2 rounded-pill">
                                    <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.5rem; height: 0.5rem;" role="status"></span>
                                    Live
                                </span>
                            </div>
                        </div>

                        {{-- Info Detail Shift Aktif --}}
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="bg-light rounded-3 p-3 text-center h-100">
                                    <small class="text-muted d-block mb-1 fw-semibold">Jenis Shift</small>
                                    @if($shiftAktif->nama_shift === 'pagi')
                                        <span class="badge fs-6" style="background-color: #fef08a; color: #854d0e; padding: 8px 16px; border-radius: 20px;">
                                            <i class="bi bi-sun me-1"></i>Pagi
                                        </span>
                                    @elseif($shiftAktif->nama_shift === 'sore')
                                        <span class="badge fs-6" style="background-color: #e0f2fe; color: #0369a1; padding: 8px 16px; border-radius: 20px;">
                                            <i class="bi bi-cloud-sun me-1"></i>Sore
                                        </span>
                                    @else
                                        <span class="badge fs-6" style="background-color: #f3e8ff; color: #6b21a8; padding: 8px 16px; border-radius: 20px;">
                                            <i class="bi bi-moon-stars me-1"></i>Malam
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-light rounded-3 p-3 text-center h-100">
                                    <small class="text-muted d-block mb-1 fw-semibold">Jam Mulai</small>
                                    <span class="fw-bold text-dark fs-5">
                                        {{ $shiftAktif->jam_mulai ? \Carbon\Carbon::parse($shiftAktif->jam_mulai)->format('H:i') : '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-light rounded-3 p-3 text-center h-100">
                                    <small class="text-muted d-block mb-1 fw-semibold">Durasi Berjalan</small>
                                    @if($shiftAktif->jam_mulai)
                                        @php
                                            $mulai = \Carbon\Carbon::parse($shiftAktif->tgl_shift->format('Y-m-d') . ' ' . $shiftAktif->jam_mulai);
                                            $menit = abs($mulai->diffInMinutes(\Carbon\Carbon::now()));
                                            $j = intdiv($menit, 60);
                                            $m = $menit % 60;
                                        @endphp
                                        <span class="fw-bold text-primary fs-6">
                                            {{ $j > 0 ? $j . 'j ' : '' }}{{ $m }}m
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        {{-- Tombol aksi --}}
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ route('kasir.pos') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm rounded-3" style="background-color: #6F4E37; border-color: #6F4E37;">
                                <i class="bi bi-calculator me-2"></i>Buka Kasir (POS)
                            </a>
                            <button type="button" class="btn btn-outline-danger px-4 py-2 fw-semibold rounded-3" data-bs-toggle="modal" data-bs-target="#modalSelesaiShift">
                                <i class="bi bi-stop-circle me-2"></i>Akhiri Shift
                            </button>
                        </div>
                    </div>
                </div>

            @else
                {{-- ═══ Kondisi: Belum Ada Shift Aktif ═══ --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div style="height: 6px; background: linear-gradient(90deg, #6F4E37, #a0724e);"></div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle p-3 me-3" style="background-color: rgba(111,78,55,0.1);">
                                <i class="bi bi-calendar-plus fs-3" style="color: #6F4E37;"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0" style="color: #6F4E37;">Mulai Shift Kerja</h5>
                                <small class="text-muted">Anda belum memiliki shift aktif hari ini</small>
                            </div>
                        </div>

                        <form action="{{ route('kasir.shift.mulai') }}" method="POST">
                            @csrf

                            {{-- Pilih Jenis Shift --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary mb-3">
                                    <i class="bi bi-list-check me-1"></i>Pilih Jenis Shift
                                </label>
                                <div class="row g-3">

                                    {{-- Pilihan: Shift Pagi --}}
                                    <div class="col-sm-4">
                                        <input type="radio" class="btn-check" name="nama_shift" id="shift_pagi" value="pagi" required
                                            {{ old('nama_shift') === 'pagi' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning w-100 py-3 rounded-4 text-start shift-option" for="shift_pagi">
                                            <div class="text-center">
                                                <i class="bi bi-sunrise fs-2 text-warning d-block mb-2"></i>
                                                <span class="fw-bold d-block">Pagi</span>
                                                <small class="text-muted">07:00 – 15:00</small>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Pilihan: Shift Sore --}}
                                    <div class="col-sm-4">
                                        <input type="radio" class="btn-check" name="nama_shift" id="shift_sore" value="sore" required
                                            {{ old('nama_shift') === 'sore' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-info w-100 py-3 rounded-4 text-start shift-option" for="shift_sore">
                                            <div class="text-center">
                                                <i class="bi bi-cloud-sun fs-2 text-info d-block mb-2"></i>
                                                <span class="fw-bold d-block">Sore</span>
                                                <small class="text-muted">15:00 – 23:00</small>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Pilihan: Shift Malam --}}
                                    <div class="col-sm-4">
                                        <input type="radio" class="btn-check" name="nama_shift" id="shift_malam" value="malam" required
                                            {{ old('nama_shift') === 'malam' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary w-100 py-3 rounded-4 text-start shift-option" for="shift_malam" style="--bs-btn-active-bg: #6b21a8; --bs-btn-active-border-color: #6b21a8;">
                                            <div class="text-center">
                                                <i class="bi bi-moon-stars fs-2 text-secondary d-block mb-2"></i>
                                                <span class="fw-bold d-block">Malam</span>
                                                <small class="text-muted">23:00 – 07:00</small>
                                            </div>
                                        </label>
                                    </div>

                                </div>
                                @error('nama_shift')
                                    <div class="text-danger small mt-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Info jam mulai --}}
                            <div class="alert alert-light border rounded-3 py-2 px-3 mb-4">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Jam mulai shift akan direkam secara otomatis saat Anda menekan tombol di bawah.
                                </small>
                            </div>

                            <button type="submit" class="btn btn-lg fw-semibold w-100 py-3 rounded-4 text-white shadow" style="background: linear-gradient(135deg, #6F4E37, #a0724e);">
                                <i class="bi bi-play-circle me-2"></i>Mulai Shift Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- ─── KANAN: Riwayat Shift Terakhir ─── --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold text-secondary mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Shift Terakhir
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($riwayatShift as $r)
                    <div class="d-flex align-items-start p-3 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }}">
                        {{-- Ikon shift --}}
                        <div class="me-3 mt-1">
                            @if($r->nama_shift === 'pagi')
                                <span class="badge rounded-3 p-2" style="background-color: #fef08a; color: #854d0e;">
                                    <i class="bi bi-sun fs-6"></i>
                                </span>
                            @elseif($r->nama_shift === 'sore')
                                <span class="badge rounded-3 p-2" style="background-color: #e0f2fe; color: #0369a1;">
                                    <i class="bi bi-cloud-sun fs-6"></i>
                                </span>
                            @else
                                <span class="badge rounded-3 p-2" style="background-color: #f3e8ff; color: #6b21a8;">
                                    <i class="bi bi-moon-stars fs-6"></i>
                                </span>
                            @endif
                        </div>
                        {{-- Info --}}
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="fw-semibold text-dark text-capitalize">Shift {{ $r->nama_shift }}</span>
                                    <small class="d-block text-muted">
                                        {{ \Carbon\Carbon::parse($r->tgl_shift)->translatedFormat('d M Y') }}
                                    </small>
                                </div>
                                {{-- Status badge --}}
                                @if($r->status === 'aktif')
                                    <span class="badge bg-success rounded-pill">Aktif</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">Selesai</span>
                                @endif
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-play me-1"></i>{{ $r->jam_mulai ? \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') : '-' }}
                                @if($r->jam_selesai)
                                    <i class="bi bi-stop ms-2 me-1"></i>{{ \Carbon\Carbon::parse($r->jam_selesai)->format('H:i') }}
                                    <span class="ms-2 text-primary fw-semibold">{{ $r->durasi }}</span>
                                @else
                                    <span class="ms-2 text-success fw-semibold">Sedang berjalan...</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0 small">Belum ada riwayat shift.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ─── Card link ke POS (jika ada shift aktif) ─── --}}
            @if($shiftAktif)
            <div class="card border-0 rounded-4 mt-3 text-white overflow-hidden" style="background: linear-gradient(135deg, #6F4E37, #a0724e);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">Siap Melayani?</h6>
                            <small class="opacity-75">Shift Anda sedang aktif. Buka kasir untuk mulai bertransaksi.</small>
                        </div>
                        <i class="bi bi-arrow-right-circle fs-1 opacity-50"></i>
                    </div>
                    <a href="{{ route('kasir.pos') }}" class="btn btn-light btn-sm fw-semibold rounded-3 mt-3 px-4">
                        <i class="bi bi-calculator me-1"></i>Buka Kasir
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ─── Modal Konfirmasi Akhiri Shift ─── --}}
@if($shiftAktif)
<div class="modal fade" id="modalSelesaiShift" tabindex="-1" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalSelesaiLabel">
                    <i class="bi bi-stop-circle text-danger me-2"></i>Akhiri Shift?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex p-3 mb-2">
                        <i class="bi bi-exclamation-triangle text-danger fs-3"></i>
                    </div>
                </div>
                <p class="text-center text-muted mb-1">Anda akan mengakhiri:</p>
                <div class="bg-light rounded-3 p-3 text-center">
                    <span class="fw-bold text-dark text-capitalize fs-5">Shift {{ $shiftAktif->nama_shift }}</span>
                    <small class="d-block text-muted mt-1">
                        Dimulai pukul {{ $shiftAktif->jam_mulai ? \Carbon\Carbon::parse($shiftAktif->jam_mulai)->format('H:i') : '-' }}
                    </small>
                </div>
                <p class="text-muted small text-center mt-3 mb-0">
                    Jam selesai akan direkam secara otomatis. Pastikan semua transaksi sudah selesai.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 gap-2">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">
                    Batal
                </button>
                <form action="{{ route('kasir.shift.selesai') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                        <i class="bi bi-stop-circle me-1"></i>Ya, Akhiri Shift
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Script: jam digital live --}}
@push('scripts')
<script>
    // Update jam live setiap detik
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const el = document.getElementById('jam-sekarang');
        if (el) el.textContent = h + ':' + m;
    }
    setInterval(updateClock, 10000);
</script>
@endpush

@endsection

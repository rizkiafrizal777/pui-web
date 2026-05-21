@extends('layouts.app')

@section('title', 'Manajemen Stok - Ngesis Coffee')
@section('header_title', 'Manajemen Stok Produk')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-secondary"><i class="bi bi-archive me-2"></i>Kelola Stok Produk</h5>
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Klik tombol untuk menambah atau mengatur stok</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok Saat Ini</th>
                        <th class="text-center" width="320">Kelola Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" alt="{{ $p->nama_produk }}"
                                     class="rounded object-fit-cover" width="45" height="45">
                            @else
                                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $p->nama_produk }}</td>
                        <td><span class="badge bg-secondary">{{ $p->kategori }}</span></td>
                        <td class="text-center">
                            @if($p->stok == 0)
                                <span class="badge bg-danger fs-6 px-3">0 (Habis)</span>
                            @elseif($p->stok <= 5)
                                <span class="badge bg-warning text-dark fs-6 px-3">{{ $p->stok }} (Menipis)</span>
                            @else
                                <span class="badge bg-success fs-6 px-3">{{ $p->stok }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center align-items-center">
                                {{-- Form Tambah Stok --}}
                                <form action="{{ route('stok.tambah', $p->id) }}" method="POST" class="d-flex gap-1 align-items-center">
                                    @csrf
                                    <input type="number" name="jumlah" class="form-control form-control-sm text-center"
                                           style="width:70px;" min="1" max="9999" value="1" required>
                                    <button type="submit" class="btn btn-sm btn-success" title="Tambah Stok">
                                        <i class="bi bi-plus-lg"></i> Tambah
                                    </button>
                                </form>

                                {{-- Tombol Set Stok (modal) --}}
                                <button class="btn btn-sm btn-outline-primary" title="Atur Stok Manual"
                                        onclick="openSetModal({{ $p->id }}, '{{ addslashes($p->nama_produk) }}', {{ $p->stok }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Set Stok --}}
<div class="modal fade" id="modalSetStok" tabindex="-1" aria-labelledby="modalSetStokLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6F4E37; color:white;">
                <h6 class="modal-title" id="modalSetStokLabel"><i class="bi bi-pencil-square me-2"></i>Atur Stok Manual</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSetStok" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-2 text-muted small">Produk: <span id="setStokNama" class="fw-bold text-dark"></span></p>
                    <label class="form-label">Stok Baru <span class="text-danger">*</span></label>
                    <input type="number" name="stok" id="setStokInput" class="form-control text-center"
                           min="0" max="9999" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background-color:#6F4E37; border-color:#6F4E37;">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openSetModal(id, nama, stokSaatIni) {
        document.getElementById('setStokNama').innerText  = nama;
        document.getElementById('setStokInput').value     = stokSaatIni;
        document.getElementById('formSetStok').action     = `/admin/stok/${id}/set`;
        new bootstrap.Modal(document.getElementById('modalSetStok')).show();
    }
</script>
@endsection

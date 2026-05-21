@extends('layouts.app')

@section('title', 'Manajemen Produk - Ngesis Coffee')
@section('header_title', 'Daftar Produk')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-secondary"><i class="bi bi-box-seam me-2"></i>Kelola Produk</h5>
        <a href="{{ route('produk.create') }}" class="btn btn-primary" style="background-color: #6F4E37; border-color: #6F4E37;">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
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
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $index => $p)
                    <tr>
                        <td>{{ $produks->firstItem() + $index }}</td>
                        <td>
                            @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" alt="{{ $p->nama_produk }}" class="rounded object-fit-cover" width="50" height="50">
                            @else
                                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $p->nama_produk }}</td>
                        <td><span class="badge bg-secondary">{{ $p->kategori }}</span></td>
                        <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td>
                            @if($p->stok == 0)
                                <span class="badge bg-danger">0 (Habis)</span>
                            @elseif($p->stok <= 5)
                                <span class="badge bg-warning text-dark">{{ $p->stok }} (Menipis)</span>
                            @else
                                <span class="badge bg-success">{{ $p->stok }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="toggleStatus{{ $p->id }}" 
                                    {{ $p->tersedia ? 'checked' : '' }} onchange="toggleStatus({{ $p->id }}, this)">
                                <label class="form-check-label" for="toggleStatus{{ $p->id }}" id="labelStatus{{ $p->id }}">
                                    {{ $p->tersedia ? 'Tersedia' : 'Tidak Tersedia' }}
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" title="Hapus" onclick="confirmDelete({{ $p->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                            <!-- Form Delete Tersembunyi -->
                            <form id="delete-form-{{ $p->id }}" action="{{ route('produk.destroy', $p->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $produks->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // AJAX Toggle Status
    function toggleStatus(id, checkbox) {
        let isChecked = checkbox.checked;
        let label = document.getElementById('labelStatus' + id);
        label.innerText = isChecked ? 'Tersedia' : 'Tidak Tersedia';

        fetch(`/admin/produk/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert jika gagal
                checkbox.checked = !isChecked;
                label.innerText = !isChecked ? 'Tersedia' : 'Tidak Tersedia';
                alert('Gagal mengubah status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert jika error network
            checkbox.checked = !isChecked;
            label.innerText = !isChecked ? 'Tersedia' : 'Tidak Tersedia';
            alert('Terjadi kesalahan jaringan');
        });
    }

    // Konfirmasi Hapus SweetAlert2
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data produk yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection

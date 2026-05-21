@extends('layouts.app')

@section('title', 'Manajemen Kategori - Ngesis Coffee')
@section('header_title', 'Manajemen Kategori')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-secondary"><i class="bi bi-tags me-2"></i>Kelola Kategori</h5>
        <a href="{{ route('kategori.create') }}" class="btn btn-primary" style="background-color: #6F4E37; border-color: #6F4E37;">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Kategori</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $index => $k)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="badge fs-6 fw-normal px-3 py-2" style="background-color:#6F4E37;">
                                <i class="bi bi-tag me-1"></i>{{ $k->nama }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kategori.edit', $k->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" title="Hapus" onclick="confirmDelete({{ $k->id }}, '{{ $k->nama }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="delete-form-{{ $k->id }}" action="{{ route('kategori.destroy', $k->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Belum ada data kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: `Kategori "${nama}" akan dihapus permanen!`,
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
        });
    }
</script>
@endsection

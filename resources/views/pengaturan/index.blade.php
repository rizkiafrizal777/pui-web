@extends('layouts.app')

@section('title', 'Pengaturan Akun - Ngesis Coffee')
@section('header_title', 'Pengaturan Akun')

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0 border-0 pt-3">
                <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i> Profil & Keamanan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pengaturan.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" value="{{ $user->nama }}" disabled>
                        <small class="text-muted">Nama tidak dapat diubah di sini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Ganti Password <small class="text-muted">(Opsional)</small></h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-white pb-0 border-0 pt-3">
                <h5 class="mb-0 text-danger"><i class="bi bi-shield-lock me-2"></i> Akses Akun</h5>
            </div>
            <div class="card-body text-center py-5">
                <i class="bi bi-person-circle display-1 text-muted mb-3 d-block"></i>
                <h5 class="mb-1">{{ $user->nama }}</h5>
                <p class="text-muted mb-4">Role: <span class="badge bg-secondary">{{ strtoupper($user->role) }}</span></p>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill shadow-sm">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout dari Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

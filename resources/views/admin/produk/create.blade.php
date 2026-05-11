@extends('layouts.app')

@section('title', 'Tambah Produk - Ngesis Coffee')
@section('header_title', 'Tambah Produk Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex align-items-center">
                <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <h5 class="mb-0 fw-bold">Form Tambah Produk</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @include('admin.produk._form')
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-light me-2">Reset</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #6F4E37; border-color: #6F4E37;">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

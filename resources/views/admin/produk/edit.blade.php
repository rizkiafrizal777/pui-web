@extends('layouts.app')

@section('title', 'Edit Produk - Ngesis Coffee')
@section('header_title', 'Edit Produk: ' . $produk->nama_produk)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex align-items-center">
                <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <h5 class="mb-0 fw-bold">Form Edit Produk</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    @include('admin.produk._form')
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" style="background-color: #6F4E37; border-color: #6F4E37;">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

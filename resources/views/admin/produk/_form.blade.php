<div class="row g-3">
    <div class="col-md-6">
        <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" id="nama_produk" name="nama_produk"
               value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required>
        @error('nama_produk')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
            <option value="">Pilih Kategori...</option>
            @php $selected_kategori = old('kategori', $produk->kategori ?? ''); @endphp
            @foreach($kategoris as $kat)
                <option value="{{ $kat }}" {{ $selected_kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
            @endforeach
        </select>
        @error('kategori')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" min="0"
               value="{{ old('harga', isset($produk->harga) ? intval($produk->harga) : '') }}" required>
        @error('harga')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="stok" class="form-label">Stok Awal <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" min="0"
               value="{{ old('stok', $produk->stok ?? 0) }}" required>
        <div class="form-text">Jumlah stok yang tersedia.</div>
        @error('stok')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="foto" class="form-label">Foto Produk (Opsional)</label>
        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto"
               accept="image/png, image/jpeg, image/webp" onchange="previewImage(this)">
        <div class="form-text">Maks. 2MB. Format: jpg, png, webp.</div>
        @error('foto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <!-- Image Preview -->
        <div class="mt-2">
            @if(isset($produk) && $produk->foto)
                <img id="imgPreview" src="{{ asset('storage/'.$produk->foto) }}" alt="Preview" class="img-thumbnail" style="max-height: 130px;">
            @else
                <img id="imgPreview" src="#" alt="Preview" class="img-thumbnail" style="max-height: 130px; display: none;">
            @endif
        </div>
    </div>

    <div class="col-12">
        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
        @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-2">
        <div class="form-check form-switch fs-5">
            <input class="form-check-input" type="checkbox" role="switch" id="tersedia" name="tersedia" value="1"
                   {{ old('tersedia', $produk->tersedia ?? true) ? 'checked' : '' }}>
            <label class="form-check-label ms-2" for="tersedia">Produk Tersedia untuk Dijual</label>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        let preview = document.getElementById('imgPreview');
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            @if(!isset($produk) || !$produk->foto)
                preview.src = '#';
                preview.style.display = 'none';
            @endif
        }
    }
</script>

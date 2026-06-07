@extends('layouts.app')

@section('title', 'POS Kasir - Ngesis Coffee')
@section('header_title', 'Point of Sale')

@section('content')
<div class="row g-3">
    <!-- Kiri: Grid Produk -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white pb-0 border-0 pt-3">
                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Cari nama produk...">
            </div>
            <div class="card-body">
                <div class="row g-3" id="productGrid">
                    @forelse($produk as $p)
                    <div class="col-md-4 product-item" data-nama="{{ strtolower($p->nama_produk) }}">
                        <div class="card h-100 product-card {{ $p->stok <= 0 ? 'opacity-75' : '' }}" role="button" onclick="addToCart({{ $p->id }}, '{{ addslashes($p->nama_produk) }}', {{ $p->harga }}, {{ $p->stok }})">
                            @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" class="card-img-top" alt="{{ $p->nama_produk }}" style="height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="bi bi-cup-hot fs-1"></i>
                                </div>
                            @endif
                            <div class="card-body p-2 text-center d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1 text-truncate">{{ $p->nama_produk }}</h6>
                                    <span class="badge bg-secondary mb-1">{{ $p->kategori->nama ?? '-' }}</span>
                                    <div class="mb-2">
                                        @if($p->stok <= 0)
                                            <span class="badge bg-danger">Habis</span>
                                        @elseif($p->stok <= 5)
                                            <span class="badge bg-warning text-dark">Sisa: {{ $p->stok }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">Stok: {{ $p->stok }}</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="card-text fw-bold text-success mb-0">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted my-5">
                        <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                        <h5>Belum ada produk yang tersedia.</h5>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Kanan: Panel Keranjang -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Keranjang Pesanan</h5>
            </div>
            
            <div class="card-body overflow-auto flex-grow-1" id="cartContainer" style="max-height: 50vh;">
                <!-- Item Keranjang (JS) -->
                <div id="emptyCart" class="text-center text-muted my-5">
                    <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                    <p>Belum ada produk di keranjang</p>
                </div>
                <div id="cartItems"></div>
            </div>

            <div class="card-footer bg-white border-top border-2">
                <div class="d-flex justify-content-between mb-3">
                    <span class="fs-5 fw-bold">TOTAL:</span>
                    <span class="fs-4 fw-bold text-success" id="cartTotal">Rp 0</span>
                </div>

                <!-- No. Meja -->
                <div class="mb-3">
                    <label class="form-label text-muted small">No. Meja (Opsional)</label>
                    <input type="text" id="noMeja" class="form-control" placeholder="Contoh: 1, 2, atau VIP...">
                </div>

                <!-- Metode Pembayaran -->
                <div class="mb-3">
                    <label class="form-label text-muted small">Metode Pembayaran</label>
                    <select id="metodeBayar" class="form-select" onchange="toggleKembalian()">
                        <option value="tunai">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>

                <!-- ── SECTION KEMBALIAN (hanya tampil jika metode = tunai) ─── -->
                <div id="sectionKembalian" class="mb-3 p-3 rounded"
                     style="background:#f0faf0; border:1px solid #c3e6cb;">

                    <label class="form-label text-muted small fw-bold">Uang Diterima</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="uangDiterima" class="form-control text-end"
                               placeholder="0" oninput="onInputUang(this)" autocomplete="off">
                    </div>

                    <!-- Tombol Nominal Cepat -->
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setNominal(5000)">5rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setNominal(10000)">10rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setNominal(20000)">20rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setNominal(50000)">50rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setNominal(100000)">100rb</button>
                        <button type="button" class="btn btn-sm btn-outline-dark"      onclick="setNominal('pas')">Uang Pas</button>
                    </div>

                    <!-- Display Kembalian -->
                    <div class="d-flex justify-content-between align-items-center border-top pt-2">
                        <span class="fw-bold">Kembalian:</span>
                        <span id="kembalianDisplay" class="fs-5 fw-bold text-muted">Rp 0</span>
                    </div>
                    <!-- Pesan error uang kurang -->
                    <div id="pesanKurang" class="text-danger small mt-1" style="display:none">
                        ⚠ Uang kurang! Tambah Rp <span id="jumlahKurang"></span>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-grid gap-2">
                    <button class="btn btn-success btn-lg" onclick="prosesBayar()" id="btnBayar" disabled>
                        <i class="bi bi-check-circle me-2"></i> BAYAR
                    </button>
                    <button class="btn btn-outline-danger" onclick="clearCart()" id="btnBatal" disabled>
                        <i class="bi bi-trash me-2"></i> BATALKAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .product-card { transition: transform 0.2s, box-shadow 0.2s; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #6F4E37; }
</style>
<script>
    // ── State ───────────────────────────────────────────────────────────────
    let cart = {};

    // ── 1. Filter Pencarian Real-time ────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        let keyword = e.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            item.style.display = item.getAttribute('data-nama').includes(keyword) ? 'block' : 'none';
        });
    });

    // ── 2. Tambah ke Keranjang ───────────────────────────────────────────────
    function addToCart(id, nama, harga, stok) {
        if (cart[id]) {
            if (cart[id].jumlah >= stok) {
                alert(`Stok untuk "${nama}" tidak mencukupi. Sisa stok: ${stok}`);
                return;
            }
            cart[id].jumlah  += 1;
            cart[id].subtotal = cart[id].jumlah * harga;
        } else {
            if (stok <= 0) {
                alert(`Stok "${nama}" sudah habis!`);
                return;
            }
            cart[id] = { id, nama, harga, jumlah: 1, subtotal: harga, stok: stok };
        }
        renderCart();
    }

    // ── 3. Render Keranjang ──────────────────────────────────────────────────
    function renderCart() {
        let cartItems = document.getElementById('cartItems');
        let emptyCart = document.getElementById('emptyCart');
        let totalEl   = document.getElementById('cartTotal');
        cartItems.innerHTML = '';
        let hasItems = false;

        for (let id in cart) {
            hasItems = true;
            let item = cart[id];
            cartItems.innerHTML += `
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div style="flex:1">
                        <h6 class="mb-0 text-truncate" style="max-width:150px">${item.nama}</h6>
                        <small class="text-muted">Rp ${formatRupiah(item.harga)}</small>
                    </div>
                    <div class="d-flex align-items-center" style="width:100px">
                        <input type="number" class="form-control form-control-sm text-center px-1"
                               value="${item.jumlah}" min="1" onchange="updateQty(${id}, this.value)">
                    </div>
                    <div class="text-end" style="width:80px">
                        <div class="fw-bold">Rp ${formatRupiah(item.subtotal)}</div>
                        <button class="btn btn-sm text-danger p-0" onclick="removeItem(${id})">
                            <small><i class="bi bi-x-circle"></i> hapus</small>
                        </button>
                    </div>
                </div>`;
        }

        totalEl.innerText = 'Rp ' + formatRupiah(getTotalCart());
        emptyCart.style.display = hasItems ? 'none' : 'block';

        // Update tombol & kembalian setiap kali cart berubah
        updateBtnBayar();
    }

    // ── 4. Update Qty ────────────────────────────────────────────────────────
    function updateQty(id, qty) {
        qty = parseInt(qty);
        if (qty < 1 || isNaN(qty)) { removeItem(id); return; }
        if (qty > cart[id].stok) {
            alert(`Stok untuk "${cart[id].nama}" tidak mencukupi. Sisa stok: ${cart[id].stok}`);
            qty = cart[id].stok;
        }
        cart[id].jumlah   = qty;
        cart[id].subtotal = cart[id].harga * qty;
        renderCart();
    }

    // ── 5. Hapus Item ────────────────────────────────────────────────────────
    function removeItem(id) { delete cart[id]; renderCart(); }

    // ── 6. Kosongkan Keranjang ───────────────────────────────────────────────
    function clearCart() {
        if (confirm('Yakin ingin membatalkan semua pesanan di keranjang?')) {
            cart = {};
            document.getElementById('noMeja').value      = '';
            document.getElementById('metodeBayar').value = 'tunai';
            document.getElementById('uangDiterima').value = '';
            toggleKembalian();
            renderCart();
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    // Format angka ke string ribuan: 18000 → "18.000"
    function formatRupiah(angka) {
        return Math.floor(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Parse string ribuan kembali ke number: "18.000" → 18000
    function parseRupiah(str) {
        return parseFloat(str.replace(/\./g, '')) || 0;
    }

    // Hitung total cart dari state
    function getTotalCart() {
        return Object.values(cart).reduce((sum, i) => sum + i.subtotal, 0);
    }

    // ── Toggle section kembalian ─────────────────────────────────────────────
    function toggleKembalian() {
        let metode  = document.getElementById('metodeBayar').value;
        let section = document.getElementById('sectionKembalian');

        if (metode === 'tunai') {
            section.style.display = 'block';
            hitungKembalian();
        } else {
            section.style.display = 'none';
            document.getElementById('uangDiterima').value       = '';
            document.getElementById('kembalianDisplay').innerText = 'Rp 0';
            document.getElementById('kembalianDisplay').className = 'fs-5 fw-bold text-muted';
            document.getElementById('pesanKurang').style.display  = 'none';
        }
    }

    // ── Input uang diterima: format otomatis saat mengetik ───────────────────
    function onInputUang(el) {
        let raw  = el.value.replace(/\D/g, '');
        el.value = raw ? formatRupiah(parseInt(raw)) : '';
        hitungKembalian();
    }

    // ── Tombol nominal cepat ─────────────────────────────────────────────────
    function setNominal(nilai) {
        let uang = (nilai === 'pas') ? getTotalCart() : nilai;
        document.getElementById('uangDiterima').value = formatRupiah(uang);
        hitungKembalian();
    }

    // ── Hitung kembalian real-time ───────────────────────────────────────────
    function hitungKembalian() {
        let total     = getTotalCart();
        let uang      = parseRupiah(document.getElementById('uangDiterima').value);
        let kembalian = uang - total;

        let elDisplay = document.getElementById('kembalianDisplay');
        let elPesan   = document.getElementById('pesanKurang');
        let elJumlah  = document.getElementById('jumlahKurang');
        let btnBayar  = document.getElementById('btnBayar');

        if (uang === 0) {
            elDisplay.innerText   = 'Rp 0';
            elDisplay.className   = 'fs-5 fw-bold text-muted';
            elPesan.style.display = 'none';
            btnBayar.disabled     = true;
        } else if (kembalian < 0) {
            elDisplay.innerText   = '- Rp ' + formatRupiah(Math.abs(kembalian));
            elDisplay.className   = 'fs-5 fw-bold text-danger';
            elPesan.style.display = 'block';
            elJumlah.innerText    = formatRupiah(Math.abs(kembalian));
            btnBayar.disabled     = true;   // kunci tombol BAYAR jika uang kurang
        } else {
            elDisplay.innerText   = 'Rp ' + formatRupiah(kembalian);
            elDisplay.className   = 'fs-5 fw-bold text-success';
            elPesan.style.display = 'none';
            btnBayar.disabled     = false;  // aktifkan tombol BAYAR
        }
    }

    // ── Update tombol BAYAR saat cart berubah ────────────────────────────────
    function updateBtnBayar() {
        let hasItems = Object.keys(cart).length > 0;
        let metode   = document.getElementById('metodeBayar').value;

        document.getElementById('btnBatal').disabled = !hasItems;

        if (!hasItems) {
            document.getElementById('btnBayar').disabled = true;
            return;
        }

        if (metode === 'tunai') {
            hitungKembalian(); // hitungKembalian yang tentukan aktif/tidaknya tombol
        } else {
            document.getElementById('btnBayar').disabled = false;
        }
    }

    // ── Proses Bayar (AJAX) ──────────────────────────────────────────────────
    function prosesBayar() {
        let metode = document.getElementById('metodeBayar').value;
        let total  = getTotalCart();

        // Validasi client: uang cukup sebelum kirim ke server
        let uangDiterima = 0;
        if (metode === 'tunai') {
            uangDiterima = parseRupiah(document.getElementById('uangDiterima').value);
            if (uangDiterima < total) {
                alert('Uang yang diterima belum cukup untuk membayar total pesanan.');
                return;
            }
        }

        // Kirim hanya id_produk dan jumlah — harga dihitung di server
        let items = Object.values(cart).map(i => ({
            id_produk : i.id,
            jumlah    : i.jumlah,
            // TIDAK mengirim harga_satuan / subtotal — server hitung dari DB
        }));

        if (items.length === 0) return;

        let btn = document.getElementById('btnBayar');
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        fetch("{{ route('pos.bayar') }}", {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                items         : items,
                metode_bayar  : metode,
                no_meja       : document.getElementById('noMeja').value,
                uang_diterima : uangDiterima,
                // total_harga TIDAK dikirim — server hitung sendiri dari DB
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url; // ke /selesai/{id}
            } else {
                alert('Gagal: ' + data.message);
                btn.disabled  = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i> BAYAR';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i> BAYAR';
        });
    }
</script>
@endsection


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Selesai — Ngesis Coffee</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ── Layout ──────────────────────────────────────────────────────────── */
        body {
            background: linear-gradient(135deg, #f5f0eb 0%, #ede4d9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 1.5rem;
        }

        .card-selesai {
            max-width: 430px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(111, 78, 55, 0.12);
            overflow: hidden;
        }

        /* ── Header ─────────────────────────────────────────────────────────── */
        .card-header-selesai {
            background: linear-gradient(135deg, #6F4E37, #8B6347);
            color: #fff;
            text-align: center;
            padding: 1.75rem 1.5rem 1.25rem;
        }

        /* ── Animasi checkmark ───────────────────────────────────────────────── */
        @keyframes popIn {
            0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
            60%  { transform: scale(1.2) rotate(5deg);  opacity: 1; }
            100% { transform: scale(1)  rotate(0deg);   opacity: 1; }
        }
        .icon-check {
            animation: popIn 0.55s cubic-bezier(.36,.07,.19,.97) forwards;
            font-size: 4rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        /* ── Card Kembalian ──────────────────────────────────────────────────── */
        .card-kembalian {
            background: #e8f5e9;
            border: 1.5px solid #a5d6a7;
            border-radius: 14px;
            padding: 1rem 1.25rem;
        }
        .kembalian-nominal {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.1;
            color: #2e7d32;
        }

        /* ── Tombol aksi ─────────────────────────────────────────────────────── */
        .btn-aksi {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.9rem 1.25rem;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            background: none;
            width: 100%;
            font-size: 0.95rem;
            color: #212529;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn-aksi:hover  { background: #fdf8f5; }
        .btn-aksi:active { background: #f5ece4; }
        .btn-aksi:last-child { border-bottom: none; }
        .btn-aksi-icon   { font-size: 1.3rem; color: #6F4E37; }

        /* ── Ringkasan item ──────────────────────────────────────────────────── */
        .section-ringkasan { padding: 1rem 1.25rem; }
        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            margin-bottom: 0.3rem;
        }
        .item-row .nama  { color: #212529; }
        .item-row .qty   { color: #6c757d; margin-left: 4px; }
        .item-row .harga { color: #495057; }

        /* ── Countdown ───────────────────────────────────────────────────────── */
        .countdown-bar {
            height: 4px;
            background: #dee2e6;
            border-radius: 0 0 20px 20px;
            overflow: hidden;
        }
        .countdown-fill {
            height: 100%;
            background: linear-gradient(90deg, #6F4E37, #c8956c);
            width: 100%;
            transition: width 1s linear;
        }

        /* ── PRINT: Struk (80mm thermal) ─────────────────────────────────────── */
        #struk-print, #pesanan-print { display: none; }

        @media print {
            /* Sembunyikan semua */
            body > * { visibility: hidden !important; }

            /* Tampilkan hanya template yang aktif */
            body.mode-struk   #struk-print,
            body.mode-struk   #struk-print *   { visibility: visible !important; }
            body.mode-pesanan #pesanan-print,
            body.mode-pesanan #pesanan-print * { visibility: visible !important; }

            #struk-print, #pesanan-print {
                position: fixed !important;
                top: 0; left: 0;
                display: block !important;
                width: 80mm;
                font-family: 'Courier New', Courier, monospace;
                font-size: 11.5px;
                line-height: 1.5;
                color: #000;
            }
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════════════════════════
     KARTU UTAMA
══════════════════════════════════════════════════════════════════════════════ --}}
<div class="card-selesai">

    {{-- Header coklat --}}
    <div class="card-header-selesai">
        <i class="bi bi-patch-check-fill icon-check"></i>
        <h4 class="mb-0 fw-light" style="letter-spacing:1px">Pesanan Selesai</h4>
        <p class="mb-0 mt-1 opacity-75 small">
            {{ $pesanan->kode_pesanan }}
            @if($pesanan->no_meja) &nbsp;·&nbsp; Meja {{ $pesanan->no_meja }} @endif
        </p>
        <p class="mb-0 opacity-60" style="font-size:0.8rem">
            {{ \Carbon\Carbon::parse($pesanan->tgl_pesanan)->format('d M Y, H:i') }}
        </p>
    </div>

    <div class="p-3">

        {{-- ── CARD KEMBALIAN (hanya jika tunai) ────────────────────────────── --}}
        @if($pesanan->metode_bayar === 'tunai')
        <div class="card-kembalian d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="text-muted small">Uang Diterima</div>
                <div class="fw-semibold fs-6">
                    Rp {{ number_format($pesanan->uang_diterima, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-end">
                <div class="text-muted small">Kembalian</div>
                <div class="kembalian-nominal">
                    Rp {{ number_format($pesanan->kembalian, 0, ',', '.') }}
                </div>
            </div>
        </div>
        @endif

        {{-- ── TOMBOL AKSI ────────────────────────────────────────────────────── --}}
        <div class="border rounded-3 mb-3" style="overflow:hidden">
            <button class="btn-aksi" onclick="printStruk()">
                <span><i class="bi bi-printer me-2 btn-aksi-icon"></i>Cetak Struk</span>
                <i class="bi bi-chevron-right text-muted"></i>
            </button>
            <button class="btn-aksi" onclick="printPesanan()">
                <span><i class="bi bi-receipt me-2 btn-aksi-icon"></i>Cetak Pesanan</span>
                <i class="bi bi-chevron-right text-muted"></i>
            </button>
            <a href="/pos" class="btn-aksi" onclick="clearCountdown()">
                <span><i class="bi bi-cart-plus me-2 btn-aksi-icon"></i>Pesanan Baru</span>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
        </div>

        {{-- ── RINGKASAN ITEM ─────────────────────────────────────────────────── --}}
        <div class="border rounded-3">
            <div class="section-ringkasan">
                @foreach($pesanan->itemPesanan as $item)
                <div class="item-row">
                    <span>
                        <span class="nama">{{ $item->produk->nama_produk }}</span>
                        <span class="qty">×{{ $item->jumlah }}</span>
                    </span>
                    <span class="harga">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach

                <div class="item-row border-top pt-2 mt-2 fw-bold" style="font-size:0.9rem">
                    <span>Total</span>
                    <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="item-row" style="font-size:0.8rem; color:#6c757d">
                    <span>Metode</span>
                    <span>{{ ucfirst($pesanan->metode_bayar) }}</span>
                </div>
                <div class="item-row" style="font-size:0.8rem; color:#6c757d">
                    <span>Kasir</span>
                    <span>{{ $pesanan->pengguna->nama }}</span>
                </div>
            </div>
        </div>

        {{-- ── COUNTDOWN ──────────────────────────────────────────────────────── --}}
        <p class="text-center text-muted mt-3 mb-1" style="font-size:0.8rem" id="countdownText">
            Pesanan baru otomatis dalam 05:00
        </p>
    </div>

    {{-- Progress bar countdown --}}
    <div class="countdown-bar">
        <div class="countdown-fill" id="countdownFill"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     TEMPLATE PRINT: STRUK (dengan harga & kembalian)
══════════════════════════════════════════════════════════════════════════════ --}}
<div id="struk-print">
    <div style="text-align:center; margin-bottom:6px">
        <strong>NGESIS COFFEE</strong><br>
        Yogyakarta<br>
        --------------------------------
    </div>
    <div>No   : {{ $pesanan->kode_pesanan }}</div>
    <div>Tgl  : {{ \Carbon\Carbon::parse($pesanan->tgl_pesanan)->format('d/m/Y H:i') }}</div>
    <div>Kasir: {{ $pesanan->pengguna->nama }}</div>
    @if($pesanan->no_meja)
    <div>Meja : {{ $pesanan->no_meja }}</div>
    @endif
    <div>--------------------------------</div>
    @foreach($pesanan->itemPesanan as $item)
    <div>{{ $item->produk->nama_produk }}</div>
    <div style="display:flex; justify-content:space-between">
        <span>&nbsp;&nbsp;{{ $item->jumlah }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
    </div>
    @endforeach
    <div>--------------------------------</div>
    <div style="display:flex; justify-content:space-between; font-weight:bold">
        <span>TOTAL</span>
        <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
    </div>
    <div>Metode: {{ ucfirst($pesanan->metode_bayar) }}</div>
    @if($pesanan->metode_bayar === 'tunai')
    <div style="display:flex; justify-content:space-between">
        <span>Tunai</span>
        <span>Rp {{ number_format($pesanan->uang_diterima, 0, ',', '.') }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; font-weight:bold">
        <span>KEMBALIAN</span>
        <span>Rp {{ number_format($pesanan->kembalian, 0, ',', '.') }}</span>
    </div>
    @endif
    <div style="text-align:center; margin-top:10px">
        --------------------------------<br>
        Terima kasih sudah berkunjung!<br>
        Selamat menikmati ☕<br>
        --------------------------------
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     TEMPLATE PRINT: PESANAN (tanpa harga — untuk dapur/barista)
══════════════════════════════════════════════════════════════════════════════ --}}
<div id="pesanan-print">
    <div style="text-align:center; margin-bottom:6px">
        <strong>NGESIS COFFEE</strong><br>
        — PESANAN —<br>
        --------------------------------
    </div>
    <div>No   : {{ $pesanan->kode_pesanan }}</div>
    <div>Tgl  : {{ \Carbon\Carbon::parse($pesanan->tgl_pesanan)->format('d/m/Y H:i') }}</div>
    @if($pesanan->no_meja)
    <div>Meja : {{ $pesanan->no_meja }}</div>
    @endif
    <div>--------------------------------</div>
    @foreach($pesanan->itemPesanan as $index => $item)
    <div style="display:flex; justify-content:space-between; font-weight:bold">
        <span>[{{ $index + 1 }}] {{ $item->produk->nama_produk }}</span>
        <span>x {{ $item->jumlah }}</span>
    </div>
    @endforeach
    <div>--------------------------------</div>
    <div>Total Item: {{ $pesanan->itemPesanan->sum('jumlah') }} pcs</div>
    <div style="text-align:center; margin-top:10px">
        --------------------------------
    </div>
</div>

<script>
// ── Print Struk (dengan harga & kembalian) ───────────────────────────────
function printStruk() {
    document.body.classList.add('mode-struk');
    document.body.classList.remove('mode-pesanan');
    window.print();
    setTimeout(() => document.body.classList.remove('mode-struk'), 800);
}

// ── Print Pesanan (tanpa harga, untuk dapur/barista) ─────────────────────
function printPesanan() {
    document.body.classList.add('mode-pesanan');
    document.body.classList.remove('mode-struk');
    window.print();
    setTimeout(() => document.body.classList.remove('mode-pesanan'), 800);
}

// ── Countdown + Progress Bar (5 menit → auto redirect ke /pos) ───────────
const DURASI   = 300; // detik
let countdown  = DURASI;
let fillEl     = document.getElementById('countdownFill');
let textEl     = document.getElementById('countdownText');

let timerInterval = setInterval(() => {
    countdown--;

    // Update teks
    let m = String(Math.floor(countdown / 60)).padStart(2, '0');
    let s = String(countdown % 60).padStart(2, '0');
    if (textEl) textEl.innerText = `Pesanan baru otomatis dalam ${m}:${s}`;

    // Shrink progress bar
    if (fillEl) fillEl.style.width = ((countdown / DURASI) * 100) + '%';

    if (countdown <= 0) {
        clearInterval(timerInterval);
        window.location.href = '/pos';
    }
}, 1000);

// Hentikan countdown jika user klik "Pesanan Baru" manual
function clearCountdown() {
    clearInterval(timerInterval);
}
</script>

</body>
</html>

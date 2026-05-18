<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ngesis Coffee POS')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
        }
        .sidebar a {
            color: #c2c7d0;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }
        .sidebar a:hover, .sidebar a.active {
            color: #fff;
            background-color: #495057;
        }
        .topbar {
            background-color: #6F4E37; /* Warna utama kopi */
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .topbar-user {
            color: white;
            text-decoration: none;
        }
        .content-area {
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-flex flex-column">
            <div class="p-3 text-center border-bottom border-secondary">
                <h5 class="mb-0">Ngesis Coffee</h5>
                <small class="text-muted">{{ ucfirst(Auth::user()->role) }} Panel</small>
            </div>
            
            <div class="mt-3 flex-grow-1">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('produk.index') }}" class="{{ request()->routeIs('produk.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Produk
                    </a>
                    <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan
                    </a>
                    <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> Pengguna
                    </a>
                    <a href="{{ route('pengaturan.index') }}" class="{{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i> Pengaturan
                    </a>
                @elseif(Auth::user()->role === 'kasir')
                    <a href="{{ route('kasir.pos') }}" class="{{ request()->routeIs('kasir.pos') ? 'active' : '' }}">
                        <i class="bi bi-calculator me-2"></i> Kasir (POS)
                    </a>
                    <a href="{{ route('kasir.riwayat') }}" class="{{ request()->routeIs('kasir.riwayat') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-2"></i> Riwayat Pesanan
                    </a>
                    <a href="{{ route('pengaturan.index') }}" class="{{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i> Pengaturan
                    </a>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 d-flex flex-column">
            <!-- Topbar -->
            <div class="topbar p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">@yield('header_title', 'Dashboard')</h5>
                </div>
                <div>
                    <span class="me-3"><i class="bi bi-person-circle"></i> {{ Auth::user()->nama }}</span>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content-area flex-grow-1">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>

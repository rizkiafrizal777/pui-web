<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Butuh Login)
Route::middleware('auth')->group(function () {
    
    // Route Khusus Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            $today = \Carbon\Carbon::today();
            
            // Statistik Hari Ini
            $pendapatanHariIni = \App\Models\Pesanan::whereDate('tgl_pesanan', $today)
                ->where('status', 'selesai')
                ->sum('total_harga');
                
            $pesananHariIni = \App\Models\Pesanan::whereDate('tgl_pesanan', $today)->count();
            
            $totalProduk = \App\Models\Produk::count();
            
            // Pesanan Terbaru
            $pesananTerbaru = \App\Models\Pesanan::with('pengguna')
                ->orderBy('tgl_pesanan', 'desc')
                ->take(5)
                ->get();

            return view('admin.dashboard', compact('pendapatanHariIni', 'pesananHariIni', 'totalProduk', 'pesananTerbaru'));
        })->name('admin.dashboard');
        
        // Manajemen Produk
        Route::resource('/produk', App\Http\Controllers\ProdukController::class)->names('produk');
        Route::patch('/produk/{id}/toggle', [App\Http\Controllers\ProdukController::class, 'toggle'])->name('produk.toggle');
        
        // Laporan Penjualan
        Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [App\Http\Controllers\LaporanController::class, 'export'])->name('laporan.export');
        
        // Manajemen Pengguna
        Route::resource('/pengguna', App\Http\Controllers\PenggunaController::class)->names('admin.pengguna');
    });

    // Route Khusus Kasir
    Route::middleware('role:kasir')->group(function () {
        // Halaman utama POS
        Route::get('/pos', [App\Http\Controllers\PosController::class, 'index'])->name('kasir.pos');

        // Proses checkout AJAX
        Route::post('/pos/bayar', [App\Http\Controllers\PosController::class, 'bayar'])->name('pos.bayar');

        // Halaman Konfirmasi Selesai (redirect target setelah bayar) — BARU
        Route::get('/selesai/{id}', [App\Http\Controllers\PosController::class, 'selesai'])->name('pos.selesai');

        // Halaman Struk (akses dari riwayat pesanan)
        Route::get('/struk/{id}', [App\Http\Controllers\PosController::class, 'struk'])->name('pos.struk');

        // Riwayat Pesanan
        Route::get('/riwayat-pesanan', [App\Http\Controllers\PosController::class, 'riwayat'])->name('kasir.riwayat');
    });

});

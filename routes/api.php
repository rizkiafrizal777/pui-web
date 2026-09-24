<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\ShiftController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (tidak perlu login)
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (harus menyertakan Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    // User info (bawaan Laravel, opsional)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Endpoint Produk
    Route::get('/produk', [ProdukController::class, 'index']);

    // Endpoint Pesanan / Transaksi
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);

    // Endpoint Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint Update Profile
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);

    // Endpoint Shift Kasir (Khusus role kasir)
    Route::middleware('role:kasir')->prefix('shift')->group(function () {
        Route::get('/status', [ShiftController::class, 'status']);
        Route::post('/mulai', [ShiftController::class, 'mulai']);
        Route::post('/selesai', [ShiftController::class, 'selesai']);
        Route::get('/riwayat', [ShiftController::class, 'riwayat']);
    });
});

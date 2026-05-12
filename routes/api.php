<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;

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

    // Endpoint Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

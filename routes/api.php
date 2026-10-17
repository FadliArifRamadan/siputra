<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PeminjamanEventController;
use App\Http\Controllers\Api\TeknisiScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StockController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/peminjaman-events', [PeminjamanEventController::class, 'index']);
// ✅ Teknisi Schedule API
Route::get('/teknisi-schedule', [TeknisiScheduleController::class, 'index'])
    ->name('api.teknisi.schedule');
Route::get('/teknisi-availability', [TeknisiScheduleController::class, 'checkAvailability'])
    ->name('api.teknisi.availability');

// ✅ Cek stok (dengan auth)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/cek-stok', [StockController::class, 'cekStok'])
        ->name('api.cek-stok');
});

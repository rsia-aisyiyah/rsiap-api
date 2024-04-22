<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\DokterController;

Route::prefix('dokter')->group(function ($router) {
    Route::get('spesialis', [DokterController::class, 'spesialis']);
    Route::get('/active/get', [DokterController::class, 'getData']);
    Route::get('/spesialis/get', [DokterController::class, 'getSpesialis']);

    Route::get('/jadwal/get', [DokterController::class, 'getJadwal']);
});

Route::middleware('jwt.verify')->prefix('dokter')->group(function ($router) {
    Route::get('/', [DokterController::class, 'index']);
    
    Route::get('/detail', [DokterController::class, 'detail']);
    Route::post('/detail', [DokterController::class, 'detail']);
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\DokterController;


Route::prefix('dokter')->group(function ($router) {
    Route::get('/active/get', [DokterController::class, 'getData']);
    Route::get('/spesialis/get', [DokterController::class, 'getSpesialis']);
});

Route::middleware('jwt.verify')->prefix('dokter')->group(function ($router) {
    Route::get('/', [DokterController::class, 'index']);
    Route::get('spesialis', [DokterController::class, 'spesialis']);
});
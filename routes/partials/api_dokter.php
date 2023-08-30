<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\DokterController;

Route::middleware('api')->prefix('dokter')->group(function ($router) {
    Route::get('/', [DokterController::class, 'index']);
    Route::get('spesialis', [DokterController::class, 'spesialis']);
});
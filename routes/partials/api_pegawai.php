<?php

use App\Http\Controllers\api\PresensiController;

Route::middleware('api')->prefix('pegawai')->group(function ($router) {
    Route::get('/', [PegawaiController::class, 'index']);
    Route::post('/presensi/tmp', [PresensiController::class, 'tmp']);
    Route::post('/presensi/rekap', [PresensiController::class, 'rekap']);
});
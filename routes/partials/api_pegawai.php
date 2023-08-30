<?php

use App\Http\Controllers\api\PresensiController;
use App\Http\Controllers\api\JadwalPegawaiController;

Route::middleware('api')->prefix('pegawai')->group(function ($router) {
    Route::get('/', [PegawaiController::class, 'index']);
    Route::post('/jadwal', [JadwalPegawaiController::class, 'index']);
    Route::post('/jadwal/now', [JadwalPegawaiController::class, 'now']);
    Route::post('/jadwal/filter', [JadwalPegawaiController::class, 'filter']);
    Route::post('/detail', [PegawaiController::class, 'detail']);
    Route::post('/presensi/tmp', [PresensiController::class, 'tmp']);
    Route::post('/presensi/rekap', [PresensiController::class, 'rekap']);
});
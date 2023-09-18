<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\CutiController;
use App\Http\Controllers\api\DiklatController;
use App\Http\Controllers\api\PegawaiController;
use App\Http\Controllers\api\PresensiController;
use App\Http\Controllers\api\RsiaKlinisController;
use App\Http\Controllers\api\JadwalPegawaiController;

Route::middleware('jwt.verify')->prefix('pegawai')->group(function ($router) {
    Route::get('/', [PegawaiController::class, 'index']);
    Route::post('/jadwal', [JadwalPegawaiController::class, 'index']);
    Route::post('/jadwal/now', [JadwalPegawaiController::class, 'now']);
    Route::post('/jadwal/filter', [JadwalPegawaiController::class, 'filter']);
    Route::post('/detail', [PegawaiController::class, 'detail']);
    
    Route::post('/cuti', [CutiController::class, 'index']);
    
    Route::post('/diklat', [DiklatController::class, 'index']);
    Route::post('/diklat/filter', [DiklatController::class, 'filter']);

    Route::post('/klinis', [RsiaKlinisController::class, 'index']);

    Route::post('/presensi/tmp', [PresensiController::class, 'tmp']);
    Route::post('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/rekap/now', [PresensiController::class, 'rekap_now']);
});
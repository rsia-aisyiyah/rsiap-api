<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\KunjunganController;

Route::middleware('jwt.verify')->prefix('kunjungan')->group(function ($router) {
    // Kunjungan Dokter
    Route::get('kunjungan', [KunjunganController::class, 'index']);
    Route::get('now', [KunjunganController::class, 'now']);
    
    Route::post('rekap', [KunjunganController::class, 'rekap']);
    Route::post('rekap/umum', [KunjunganController::class, 'rekapUmum']);
    Route::post('rekap/radiologi', [KunjunganController::class, 'rekapRadiologi']);


    Route::get('{tahun}', [KunjunganController::class, 'byDate']);
    Route::get('{tahun}/{bulan}', [KunjunganController::class, 'byDate']);
    Route::get('{tahun}/{bulan}/{tanggal}', [KunjunganController::class, 'byDate']);
});
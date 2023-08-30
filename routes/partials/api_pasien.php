<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\PasienController;
use App\Http\Controllers\api\OperasiController;
use App\Http\Controllers\api\PasienRalanController;
use App\Http\Controllers\api\PasienRanapController;

Route::middleware('api')->prefix('pasien')->group(function ($router) {
    // Semua Pasien (termasuk rawat inap dan rawat jalan)
    Route::get('/', [PasienController::class, 'index']);
    Route::get('now', [PasienController::class, 'now']);
    Route::get('metric/now', [PasienController::class, 'metricNow']);    
    Route::post('search', [PasienController::class, 'search']);

    // Pasien Rawat Inap
    Route::get('ranap/now', [PasienRanapController::class, 'now']);
    Route::get('ranap/all', [PasienRanapController::class, 'all']);
    Route::get('ranap', [PasienRanapController::class, 'index']);

    // Pasien Rawat Jalan
    Route::get('ralan', [PasienRalanController::class, 'index']);
    Route::get('ralan/now', [PasienRalanController::class, 'now']);

    // Pemeriksaan Pasien
    Route::post('pemeriksaan', [PasienController::class, 'pemeriksaan']);
    Route::post('pemeriksaan/chart', [PasienController::class, 'pemeriksaanChart']);
    Route::post('pemeriksaan/verify', [PasienController::class, 'verifikasiSoap']);

    // Operasi 
    Route::get('operasi', [OperasiController::class, 'index']);
    Route::post('operasi/data', [OperasiController::class, 'data']);
    Route::post('operasi/filter', [OperasiController::class, 'filter']);

    // Pasien Rawat Inap With Dynamic Parameter
    Route::get('ranap/{tahun}', [PasienRanapController::class, 'byDate']);
    Route::get('ranap/{tahun}/{bulan}', [PasienRanapController::class, 'byDate']);
    Route::get('ranap/{tahun}/{bulan}/{tanggal}', [PasienRanapController::class, 'byDate']);

    // Pasien Rawat Jalan With Dynamic Parameter
    Route::get('ralan/{tahun}', [PasienRalanController::class, 'byDate']);
    Route::get('ralan/{tahun}/{bulan}', [PasienRalanController::class, 'byDate']);
    Route::get('ralan/{tahun}/{bulan}/{tanggal}', [PasienRalanController::class, 'byDate']);

    // Pasien With Dynamic Parameter
    Route::get('{tahun}', [PasienController::class, 'byDate']);
    Route::get('{tahun}/{bulan}', [PasienController::class, 'byDate']);
    Route::get('{tahun}/{bulan}/{tanggal}', [PasienController::class, 'byDate']);
});
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\JadwalOperasiController;

Route::middleware('jwt.verify')->prefix('jadwal')->group(function ($router) {
    // Jadwal Operasi Dokter
    Route::get('operasi', [JadwalOperasiController::class, 'index']);
    Route::get('operasi/now', [JadwalOperasiController::class, 'now']);

    Route::get('operasi/{tahun}', [JadwalOperasiController::class, 'byDate']);
    Route::get('operasi/{tahun}/{bulan}', [JadwalOperasiController::class, 'byDate']);
    Route::get('operasi/{tahun}/{bulan}/{tanggal}', [JadwalOperasiController::class, 'byDate']);
});
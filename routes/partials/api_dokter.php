<?php

use App\Http\Controllers\api\dokter\SmtpController;
use App\Http\Controllers\api\dokter\DokterController;
use App\Http\Controllers\api\dokter\PasienController;
use App\Http\Controllers\api\dokter\OperasiController;
use App\Http\Controllers\api\dokter\JasaMedisController;
use App\Http\Controllers\api\dokter\KunjunganController;
use App\Http\Controllers\api\dokter\PasienRalanController;
use App\Http\Controllers\api\dokter\PasienRanapController;
use App\Http\Controllers\api\dokter\JadwalOperasiController;

Route::middleware('api')->prefix('dokter')->group(function ($router) {
    Route::get('/', [DokterController::class, 'index']);
    Route::get('spesialis', [DokterController::class, 'spesialis']);

    // Pasien Rawat Inap
    Route::get('pasien/ranap', [PasienRanapController::class, 'index']);
    Route::get('pasien/ranap/all', [PasienRanapController::class, 'all']);
    Route::get('pasien/ranap/now', [PasienRanapController::class, 'now']);

    Route::get('pasien/ranap/{tahun}', [PasienRanapController::class, 'byDate']);
    Route::get('pasien/ranap/{tahun}/{bulan}', [PasienRanapController::class, 'byDate']);
    Route::get('pasien/ranap/{tahun}/{bulan}/{tanggal}', [PasienRanapController::class, 'byDate']);

    // Pasien Rawat Jalan
    Route::get('pasien/ralan', [PasienRalanController::class, 'index']);
    Route::get('pasien/ralan/now', [PasienRalanController::class, 'now']);

    Route::get('pasien/ralan/{tahun}', [PasienRalanController::class, 'byDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}', [PasienRalanController::class, 'byDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}/{tanggal}', [PasienRalanController::class, 'byDate']);

    // Jasa Medis Dokter
    Route::get('jasamedis', [JasaMedisController::class, 'index']);

    // Email SMTP
    Route::get('smtp', [SmtpController::class, 'index']);

    // Pasien Rawat Jalan
    Route::get('pasien/ralan/now', [PasienRalanController::class, 'now']);
    Route::get('pasien/ralan/{tahun}', [PasienRalanController::class, 'byDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}', [PasienRalanController::class, 'byDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}/{tanggal}', [PasienRalanController::class, 'byDate']);

    // Pasien POST
    Route::post('pasien/search', [PasienController::class, 'search']);
    Route::post('pasien/pemeriksaan', [PasienController::class, 'pemeriksaan']);
    Route::post('pasien/pemeriksaan/chart', [PasienController::class, 'pemeriksaanChart']);
    Route::post('pasien/pemeriksaan/verify', [PasienController::class, 'verifikasiSoap']);

    // Semua Pasien (termasuk rawat inap dan rawat jalan)
    Route::get('pasien', [PasienController::class, 'index']);
    Route::get('pasien/now', [PasienController::class, 'now']);
    Route::get('pasien/metric/now', [PasienController::class, 'metricNow']);

    Route::get('pasien/{tahun}', [PasienController::class, 'byDate']);
    Route::get('pasien/{tahun}/{bulan}', [PasienController::class, 'byDate']);
    Route::get('pasien/{tahun}/{bulan}/{tanggal}', [PasienController::class, 'byDate']);

    // Jadwal Operasi Dokter
    Route::get('jadwal/operasi', [JadwalOperasiController::class, 'index']);
    Route::get('jadwal/operasi/now', [JadwalOperasiController::class, 'now']);

    Route::get('jadwal/operasi/{tahun}', [JadwalOperasiController::class, 'byDate']);
    Route::get('jadwal/operasi/{tahun}/{bulan}', [JadwalOperasiController::class, 'byDate']);
    Route::get('jadwal/operasi/{tahun}/{bulan}/{tanggal}', [JadwalOperasiController::class, 'byDate']);


    Route::post('operasi/data', [OperasiController::class, 'data']);
    Route::post('operasi/filter', [OperasiController::class, 'filter']);

    Route::get('operasi', [OperasiController::class, 'index']);

    // Kunjungan Dokter
    Route::post('kunjungan/rekap', [KunjunganController::class, 'rekap']);

    Route::get('kunjungan', [KunjunganController::class, 'index']);
    Route::get('kunjungan/now', [KunjunganController::class, 'now']);

    Route::get('kunjungan/{tahun}', [KunjunganController::class, 'byDate']);
    Route::get('kunjungan/{tahun}/{bulan}', [KunjunganController::class, 'byDate']);
    Route::get('kunjungan/{tahun}/{bulan}/{tanggal}', [KunjunganController::class, 'byDate']);
});
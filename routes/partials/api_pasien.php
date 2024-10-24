<?php

use App\Http\Controllers\RadiologiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\PasienController;
use App\Http\Controllers\api\OperasiController;
use App\Http\Controllers\api\PasienAuth;
use App\Http\Controllers\api\PasienRalanController;
use App\Http\Controllers\api\PasienRanapController;


Route::prefix('pasien')->group(function ($router) {
    // auth group
    $router->prefix('auth')->group(function ($r) {
        $r->post('register', [PasienAuth::class, 'register']);
        $r->post('login', [PasienAuth::class, 'login']);
        $r->middleware('jwt.verify')->post('logout', [PasienAuth::class, 'logout']);
    });
});

Route::middleware('jwt.verify')->prefix('pasien')->group(function ($router) {
    
    $router->prefix('auth')->group(function ($r) {
        $r->post('logout', [PasienAuth::class, 'logout']);
        $r->get('validate', [PasienAuth::class, 'validateToken']);
        $r->get('user', [PasienAuth::class, 'getUser']);
    });

    // Semua Pasien (termasuk rawat inap dan rawat jalan)
    Route::get('/', [PasienController::class, 'index']);
    Route::get('now', [PasienController::class, 'now']);
    Route::get('metric/now', [PasienController::class, 'metricNow']);
    Route::get('metric/radiologi/now', [PasienController::class, 'metricRadiologiNow']);
    Route::post('search', [PasienController::class, 'search']);

    // Pasien Ranap Gabung
    Route::get('ranap/gabung', [PasienRanapController::class, 'gabung']);

    // Pasien Rawat Inap
    Route::get('ranap', [PasienRanapController::class, 'index']);
    Route::get('ranap/now', [PasienRanapController::class, 'now']);
    Route::get('ranap/all', [PasienRanapController::class, 'all']); // <- pasien rawat inap belum pulang

    // Resume Pasien Ranap
    Route::get('ranap/resume', [PasienRanapController::class, 'resume']);
    Route::post('ranap/resume', [PasienRanapController::class, 'resume']);
    Route::post('ranap/resume/verify', [PasienRanapController::class, 'verifyResume']);

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
    
    // Radiologi
    Route::prefix('radiologi')->group(function ($r) {
        $r->get('/', [RadiologiController::class, 'index']);
        $r->post('/', [RadiologiController::class, 'index']);
        $r->get('now', [RadiologiController::class, 'now']);
        $r->post('hasil', [RadiologiController::class, 'hasil']);
        $r->get('permintaan', [RadiologiController::class, 'permintaan']);
        $r->get('permintaan/now', [RadiologiController::class, 'permintaanNow']);
        $r->post('hasil/get', [RadiologiController::class, 'getHasil']);
        $r->post('hasil/store', [RadiologiController::class, 'storeHasil']);
    });


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

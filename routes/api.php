<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DokterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return isOk('API is running');
});

// Auth without middleware
Route::prefix('auth')->group(function ($router) {
    Route::post('login', [AuthController::class, 'login']);
});

// Auth
Route::middleware('api')->prefix('auth')->group(function ($router) {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('validate', [AuthController::class, 'validateToken']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// Dokter Endpoints
Route::middleware('api')->prefix('dokter')->group(function ($router) {
    Route::get('/', [DokterController::class, 'index']);
    Route::get('spesialis', [DokterController::class, 'spesialis']);
    
    // Semua Pasien (termasuk rawat inap dan rawat jalan)
    Route::get('pasien', [DokterController::class, 'pasien']);
    Route::get('pasien/now', [DokterController::class, 'pasienNow']);
    Route::get('pasien/{tahun}', [DokterController::class, 'pasienByDate']);
    Route::get('pasien/{tahun}/{bulan}', [DokterController::class, 'pasienByDate']);
    Route::get('pasien/{tahun}/{bulan}/{tanggal}', [DokterController::class, 'pasienByDate']);
    
    // Pasien Rawat Inap
    Route::get('pasien/ranap', [DokterController::class, 'pasienRawatInap']);
    Route::get('pasien/ranap/now', [DokterController::class, 'pasienRawatInapNow']);
    Route::get('pasien/ranap/{tahun}', [DokterController::class, 'pasienRawatInapByDate']);
    Route::get('pasien/ranap/{tahun}/{bulan}', [DokterController::class, 'pasienRawatInapByDate']);
    Route::get('pasien/ranap/{tahun}/{bulan}/{tanggal}', [DokterController::class, 'pasienRawatInapByDate']);
    
    // Pasien Rawat Jalan
    Route::get('pasien/ralan', [DokterController::class, 'pasienRawatJalan']);
    Route::get('pasien/ralan/now', [DokterController::class, 'pasienRawatJalanNow']);
    Route::get('pasien/ralan/{tahun}', [DokterController::class, 'pasienRawatJalanByDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}', [DokterController::class, 'pasienRawatJalanByDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}/{tanggal}', [DokterController::class, 'pasienRawatJalanByDate']);

    // Jadwal Operasi Dokter
    Route::get('jadwal/operasi', [DokterController::class, 'jadwalOperasi']);
    Route::get('jadwal/operasi/now', [DokterController::class, 'jadwalOperasiNow']);
    Route::get('jadwal/operasi/{tahun}', [DokterController::class, 'jadwalOperasiByDate']);
    Route::get('jadwal/operasi/{tahun}/{bulan}', [DokterController::class, 'jadwalOperasiByDate']);
    Route::get('jadwal/operasi/{tahun}/{bulan}/{tanggal}', [DokterController::class, 'jadwalOperasiByDate']);

    // Kunjungan Dokter
    Route::get('kunjungan', [DokterController::class, 'kunjunganDokter']);
    Route::get('kunjungan/now', [DokterController::class, 'kunjunganDokterNow']);
    Route::get('kunjungan/{tahun}', [DokterController::class, 'kunjunganDokterByDate']);
    Route::get('kunjungan/{tahun}/{bulan}', [DokterController::class, 'kunjunganDokterByDate']);
    Route::get('kunjungan/{tahun}/{bulan}/{tanggal}', [DokterController::class, 'kunjunganDokterByDate']);
});


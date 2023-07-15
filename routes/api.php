<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;

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
    Route::get('/', [App\Http\Controllers\api\dokter\DokterController::class, 'index']);
    Route::get('spesialis', [App\Http\Controllers\api\dokter\DokterController::class, 'spesialis']);

    // Semua Pasien (termasuk rawat inap dan rawat jalan)
    Route::get('pasien', [App\Http\Controllers\api\dokter\PasienController::class, 'pasien']);
    Route::get('pasien/now', [App\Http\Controllers\api\dokter\PasienController::class, 'pasienNow']);
    Route::get('pasien/{tahun}', [App\Http\Controllers\api\dokter\PasienController::class, 'pasienByDate']);
    Route::get('pasien/{tahun}/{bulan}', [App\Http\Controllers\api\dokter\PasienController::class, 'pasienByDate']);
    Route::get('pasien/{tahun}/{bulan}/{tanggal}', [App\Http\Controllers\api\dokter\PasienController::class, 'pasienByDate']);

    // Pasien Rawat Inap
    Route::get('pasien/ranap', [App\Http\Controllers\api\dokter\PasienRanapController::class, 'pasienRawatInap']);
    Route::get('pasien/ranap/now', [App\Http\Controllers\api\dokter\PasienRanapController::class, 'pasienRawatInapNow']);
    Route::get('pasien/ranap/{tahun}', [App\Http\Controllers\api\dokter\PasienRanapController::class, 'pasienRawatInapByDate']);
    Route::get('pasien/ranap/{tahun}/{bulan}', [App\Http\Controllers\api\dokter\PasienRanapController::class, 'pasienRawatInapByDate']);
    Route::get('pasien/ranap/{tahun}/{bulan}/{tanggal}', [App\Http\Controllers\api\dokter\PasienRanapController::class, 'pasienRawatInapByDate']);

    // Pasien Rawat Jalan
    Route::get('pasien/ralan', [App\Http\Controllers\api\dokter\PasienRalanController::class, 'pasienRawatJalan']);
    Route::get('pasien/ralan/now', [App\Http\Controllers\api\dokter\PasienRalanController::class, 'pasienRawatJalanNow']);
    Route::get('pasien/ralan/{tahun}', [App\Http\Controllers\api\dokter\PasienRalanController::class, 'pasienRawatJalanByDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}', [App\Http\Controllers\api\dokter\PasienRalanController::class, 'pasienRawatJalanByDate']);
    Route::get('pasien/ralan/{tahun}/{bulan}/{tanggal}', [App\Http\Controllers\api\dokter\PasienRalanController::class, 'pasienRawatJalanByDate']);

    // Jadwal Operasi Dokter
    Route::get('jadwal/operasi', [App\Http\Controllers\api\dokter\JadwalOperasiController::class, 'jadwalOperasi']);
    Route::get('jadwal/operasi/now', [App\Http\Controllers\api\dokter\JadwalOperasiController::class, 'jadwalOperasiNow']);
    Route::get('jadwal/operasi/{tahun}', [App\Http\Controllers\api\dokter\JadwalOperasiController::class, 'jadwalOperasiByDate']);
    Route::get('jadwal/operasi/{tahun}/{bulan}', [App\Http\Controllers\api\dokter\JadwalOperasiController::class, 'jadwalOperasiByDate']);
    Route::get('jadwal/operasi/{tahun}/{bulan}/{tanggal}', [App\Http\Controllers\api\dokter\JadwalOperasiController::class, 'jadwalOperasiByDate']);

    // Kunjungan Dokter
    Route::get('kunjungan', [App\Http\Controllers\api\dokter\KunjunganController::class, 'kunjunganDokter']);
    Route::get('kunjungan/now', [App\Http\Controllers\api\dokter\KunjunganController::class, 'kunjunganDokterNow']);
    Route::get('kunjungan/{tahun}', [App\Http\Controllers\api\dokter\KunjunganController::class, 'kunjunganDokterByDate']);
    Route::get('kunjungan/{tahun}/{bulan}', [App\Http\Controllers\api\dokter\KunjunganController::class, 'kunjunganDokterByDate']);
    Route::get('kunjungan/{tahun}/{bulan}/{tanggal}', [App\Http\Controllers\api\dokter\KunjunganController::class, 'kunjunganDokterByDate']);
});

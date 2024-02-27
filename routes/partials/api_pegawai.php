<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\CutiController;
use App\Http\Controllers\api\DiklatController;
use App\Http\Controllers\api\PegawaiController;
use App\Http\Controllers\api\PresensiController;
use App\Http\Controllers\api\RsiaKlinisController;
use App\Http\Controllers\api\JadwalPegawaiController;
use App\Http\Controllers\api\BerkasController;
use App\Http\Controllers\api\JasaMedisController;
use App\Models\RsiaBerkasPegawai;

Route::middleware('jwt.verify', 'cors')->prefix('pegawai')->group(function ($router) {
    Route::post('/profile/upload', [PegawaiController::class, 'profile_upload']);
});

Route::middleware('jwt.verify')->prefix('pegawai')->group(function ($router) {
    Route::get('/', [PegawaiController::class, 'index']);
    Route::get('/get/mengetahui', [PegawaiController::class, 'getMengetahui']);
    Route::get('/get/simple', [PegawaiController::class, 'get_simple']);
    Route::get('/get/{nik}', [PegawaiController::class, 'get']);
    
    Route::get('/get_lsdt', [PegawaiController::class, 'get_lsdt']);

    Route::post('/store', [PegawaiController::class, 'store']);
    Route::post('/detail', [PegawaiController::class, 'detail']);
    Route::post('/update', [PegawaiController::class, 'update']);
    Route::delete('/destroy', [PegawaiController::class, 'destroy']);

    Route::post('/jadwal', [JadwalPegawaiController::class, 'index']);
    Route::post('/jadwal/now', [JadwalPegawaiController::class, 'now']);
    Route::post('/jadwal/filter', [JadwalPegawaiController::class, 'filter']);
    Route::post('/get/berkas', [BerkasController::class, 'get_berkas']);
    Route::post('/upload/berkas', [BerkasController::class, 'upload']);
    Route::post('/delete/berkas', [BerkasController::class, 'delete']);

    Route::get('/berkas/kategori', [BerkasController::class, 'get_kategori']);
    Route::get('/berkas/nama-berkas', [BerkasController::class, 'get_nama_berkas']);
    
    Route::post('/berkas-pegawai', [BerkasController::class, 'index']);

    Route::post('/cuti', [CutiController::class, 'index']);
    Route::post('/cuti/post', [CutiController::class, 'simpanCuti']);
    Route::post('/cuti/count', [CutiController::class, 'counterCuti']);
    Route::delete('/cuti/delete', [CutiController::class, 'hapusCuti']);
    
    Route::post('/diklat', [DiklatController::class, 'index']);
    Route::post('/diklat/filter', [DiklatController::class, 'filter']);

    Route::post('/klinis', [RsiaKlinisController::class, 'index']);

    Route::post('/presensi/tmp', [PresensiController::class, 'tmp']);
    Route::post('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/rekap/now', [PresensiController::class, 'rekap_now']);

    Route::post('/update-email', [PegawaiController::class, 'updateEmail']);
    Route::post('/update-profil', [PegawaiController::class, 'updateProfil']);

});
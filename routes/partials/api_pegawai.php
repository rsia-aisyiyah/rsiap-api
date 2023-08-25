<?php

use App\Http\Controllers\api\pegawai\PegawaiController;

Route::middleware('api')->prefix('pegawai')->group(function ($router) {
    Route::get('/', [PegawaiController::class, 'index']);
});
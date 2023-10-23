<?php

use App\Http\Controllers\api\MonitorResumePasien;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\MonitorRmeController;

Route::middleware('jwt.verify')->prefix('monitor')->group(function ($router) {
    Route::get('resume/ranap', [MonitorResumePasien::class, 'ranap']);

    Route::get('rme/ugd', [MonitorRmeController::class, 'ugd']);
    Route::get('rme/ranap', [MonitorRmeController::class, 'ranap']);
    
    Route::post('rme/ugd', [MonitorRmeController::class, 'ugd']);
    Route::post('rme/ranap', [MonitorRmeController::class, 'ranap']);

    Route::get('pengisian-erm/spesialis/ranap', [MonitorRmeController::class, 'ermSpesialistRanap']);
    Route::get('pengisian-erm/spesialis/ralan', [MonitorRmeController::class, 'ermSpesialistRalan']);
    Route::get('pengisian-erm/spesialis/ralan/debug', [MonitorRmeController::class, 'ermSpesialistRalanDebug']);
});
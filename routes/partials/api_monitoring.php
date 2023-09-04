<?php

use App\Http\Controllers\api\MonitorResumePasien;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\MonitorRmeController;

Route::middleware('api')->prefix('monitor')->group(function ($router) {
    Route::get('rme/ugd', [MonitorRmeController::class, 'ugd']);
    Route::get('rme/ranap', [MonitorRmeController::class, 'ranap']);
    
    Route::get('resume/ranap', [MonitorResumePasien::class, 'ranap']);
    
    Route::post('rme/ugd', [MonitorRmeController::class, 'ugd']);
    Route::post('rme/ranap', [MonitorRmeController::class, 'ranap']);
});
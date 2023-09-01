<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\MonitorRmeController;

Route::middleware('api')->prefix('monitor')->group(function ($router) {
    Route::get('rme/ugd', [MonitorRmeController::class, 'ugd']);
    Route::get('rme/ranap', [MonitorRmeController::class, 'ranap']);
});
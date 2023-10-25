<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\DokterController;
use App\Http\Controllers\GudangFarmasiController;

Route::middleware('jwt.verify')->prefix('farmasi')->group(function ($router) {
    $router->prefix('gudang')->group(function ($r) {
        $r->post('metrics', [GudangFarmasiController::class, 'metrics']);
        $r->post('metrics/top/obat', [GudangFarmasiController::class, 'topObat']);
        $r->post('metrics/bottom/obat', [GudangFarmasiController::class, 'topObatBottom']);
        $r->post('metrics/detail', [GudangFarmasiController::class, 'metricsDetail']);
        
        $r->prefix('pesanan')->group(function ($rt) {
            $rt->get('/', [GudangFarmasiController::class, 'pesanan']);
        });
    });

});
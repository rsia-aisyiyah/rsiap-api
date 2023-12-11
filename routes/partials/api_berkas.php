<?php

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('berkas')->group(function ($router) {
    $router->group(['prefix' => 'pks'], function () use ($router) {
        $router->get('/', [\App\Http\Controllers\api\PksController::class, 'index']);
        $router->get('/last-nomor', [\App\Http\Controllers\api\PksController::class, 'getLastNomor']);
        $router->post('/', [\App\Http\Controllers\api\PksController::class, 'store']);
        $router->post('/{id}', [\App\Http\Controllers\api\PksController::class, 'update']);
        $router->delete('/{id}', [\App\Http\Controllers\api\PksController::class, 'destroy']);
    });

    $router->group(['prefix' => 'spo'], function () use ($router) {
        $router->get('/', [\App\Http\Controllers\RsiaSpoController::class, 'index']);
        $router->get('/show', [\App\Http\Controllers\RsiaSpoController::class, 'show']);
        $router->post('/show', [\App\Http\Controllers\RsiaSpoController::class, 'show']);
        $router->post('/create', [\App\Http\Controllers\RsiaSpoController::class, 'store']);
        $router->post('/update', [\App\Http\Controllers\RsiaSpoController::class, 'update']);
        $router->delete('/delete', [\App\Http\Controllers\RsiaSpoController::class, 'destroy']);
    });
});

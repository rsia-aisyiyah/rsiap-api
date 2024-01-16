<?php

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('berkas')->group(function ($router) {
    $router->group(['prefix' => 'pks'], function () use ($router) {
        $router->get('/', [\App\Http\Controllers\api\PksController::class, 'index']);
        $router->post('/', [\App\Http\Controllers\api\PksController::class, 'store']);
        $router->post('/{id}', [\App\Http\Controllers\api\PksController::class, 'update']);
        $router->delete('/{id}', [\App\Http\Controllers\api\PksController::class, 'delete']);
        
        $router->delete('/{id}/destroy', [\App\Http\Controllers\api\PksController::class, 'destroy']);
        $router->get('/last-nomor', [\App\Http\Controllers\api\PksController::class, 'getLastNomor']);
    });

    $router->group(['prefix' => 'spo'], function () use ($router) {
        $router->get('/', [\App\Http\Controllers\RsiaSpoController::class, 'index']);
        $router->get('/show', [\App\Http\Controllers\RsiaSpoController::class, 'show']);
        $router->post('/show', [\App\Http\Controllers\RsiaSpoController::class, 'show']);
        $router->post('/create', [\App\Http\Controllers\RsiaSpoController::class, 'store']);
        $router->post('/update', [\App\Http\Controllers\RsiaSpoController::class, 'update']);
        $router->delete('/delete', [\App\Http\Controllers\RsiaSpoController::class, 'delete']);
        
        // $router->delete('/destroy', [\App\Http\Controllers\RsiaSpoController::class, 'destroy']);
        $router->get('/last-nomor', [\App\Http\Controllers\RsiaSpoController::class, 'getLastNomor']);
        
        $router->group(['prefix' => 'detail'], function () use ($router) {
            $router->get('/', [\App\Http\Controllers\api\RsiaSpoDetailController::class, 'index']);
            $router->post('/store', [\App\Http\Controllers\api\RsiaSpoDetailController::class, 'store']);
        });
    });

    $router->group(['prefix' => 'sk'], function () use ($router) {
        $router->get('/', [\App\Http\Controllers\RsiaSkController::class, 'index']);
        $router->post('/store', [\App\Http\Controllers\RsiaSkController::class, 'store']);
        $router->post('/update', [\App\Http\Controllers\RsiaSkController::class, 'update']);
        $router->post('/delete', [\App\Http\Controllers\RsiaSkController::class, 'delete']);

        $router->delete('/destroy', [\App\Http\Controllers\RsiaSkController::class, 'destroy']);
    });
});

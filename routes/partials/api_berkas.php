<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('berkas')->group(function ($router) {
    $router->get('pks', [\App\Http\Controllers\api\PksController::class, 'index']);
    $router->get('pks/last-nomor', [\App\Http\Controllers\api\PksController::class, 'getLastNomor']);
    $router->post('pks', [\App\Http\Controllers\api\PksController::class, 'store']);
    $router->post('pks/{id}', [\App\Http\Controllers\api\PksController::class, 'update']);
    $router->delete('pks/{id}', [\App\Http\Controllers\api\PksController::class, 'destroy']);
});
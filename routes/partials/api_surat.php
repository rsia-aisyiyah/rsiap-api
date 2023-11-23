<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('surat')->group(function ($router) {
    $router->get('internal', [\App\Http\Controllers\RsiaSuratInternalController::class, 'index']);
    $router->get('get/by', [\App\Http\Controllers\RsiaSuratInternalController::class, 'get_by']);

    $router->post('create', [\App\Http\Controllers\RsiaSuratInternalController::class, 'create']);
    $router->delete('destroy', [\App\Http\Controllers\RsiaSuratInternalController::class, 'destroy']);
});
<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('surat')->group(function ($router) {
    $router->group(['prefix' => 'internal'], function () use ($router) {
        $router->get('/', [\App\Http\Controllers\RsiaSuratInternalController::class, 'index']);
        $router->get('get/calendar', [\App\Http\Controllers\RsiaSuratInternalController::class, 'getCalendar']);
        $router->get('/detail', [\App\Http\Controllers\RsiaSuratInternalController::class, 'detail']);
        $router->post('/detail', [\App\Http\Controllers\RsiaSuratInternalController::class, 'detail']);
        $router->post('/create', [\App\Http\Controllers\RsiaSuratInternalController::class, 'create']);
        $router->post('/update', [\App\Http\Controllers\RsiaSuratInternalController::class, 'update']);
        $router->post('/update/status', [\App\Http\Controllers\RsiaSuratInternalController::class, 'update_status']);
        $router->delete('/destroy', [\App\Http\Controllers\RsiaSuratInternalController::class, 'destroy']);

        $router->get('/metrics', [\App\Http\Controllers\RsiaSuratInternalController::class, 'metrics']);
    });
    $router->get('get/by', [\App\Http\Controllers\RsiaSuratInternalController::class, 'get_by']);
});
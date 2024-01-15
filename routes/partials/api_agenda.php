<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('agenda')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\RsiaAgendaController::class, 'index']);
    $router->get('/calendar', [\App\Http\Controllers\RsiaAgendaController::class, 'calendar']);
    $router->get('/{id}', [\App\Http\Controllers\RsiaAgendaController::class, 'show']);
    $router->post('/', [\App\Http\Controllers\RsiaAgendaController::class, 'store']);
    $router->post('/{id}', [\App\Http\Controllers\RsiaAgendaController::class, 'update']);
    $router->post('/{id}/status', [\App\Http\Controllers\RsiaAgendaController::class, 'updateStatus']);
    $router->delete('/{id}', [\App\Http\Controllers\RsiaAgendaController::class, 'delete']);

    // destroy
    $router->delete('/destroy/{id}', [\App\Http\Controllers\RsiaAgendaController::class, 'destroy']);
});
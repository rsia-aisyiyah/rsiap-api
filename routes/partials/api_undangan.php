<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('undangan')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\api\UndanganController::class, 'index']);
    $router->get('/me', [\App\Http\Controllers\api\UndanganController::class, 'me']);
    
    $router->post('/detail', [\App\Http\Controllers\api\UndanganController::class, 'detail']);

    $router->group(['prefix' => 'kegiatan', 'middleware' => 'departmen:ADM,DIR,DM10,DM9,IT'], function () use ($router) {
        $router->post('present', [\App\Http\Controllers\api\UndanganController::class, 'present']);
    });
});
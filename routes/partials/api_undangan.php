<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('undangan')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\api\UndanganController::class, 'index']);
    $router->get('/me', [\App\Http\Controllers\api\UndanganController::class, 'me']);
    
    $router->post('/detail', [\App\Http\Controllers\api\UndanganController::class, 'detail']);
    $router->post('/penerima', [\App\Http\Controllers\api\UndanganController::class, 'penerima']);

    // 'middleware' => 'departmen:ADM,DIR,DM10,DM9,IT'
    $router->group(['prefix' => 'kegiatan'], function () use ($router) {
        $router->post('present', [\App\Http\Controllers\api\UndanganController::class, 'present']);
        $router->post('tambah/presensi', [\App\Http\Controllers\api\UndanganController::class, 'tambahPresensi']);
    });
});
<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('resiko')->group(function ($router) {
    $router->middleware('jwt.verify')->prefix('kerja')->group(function ($rt) {
        $rt->get('/', [\App\Http\Controllers\api\ResikoController::class, 'resiko_kerja']);
    });
});
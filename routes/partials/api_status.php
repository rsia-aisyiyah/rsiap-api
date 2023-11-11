<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('status')->group(function ($router) {
    $router->middleware('jwt.verify')->prefix('kerja')->group(function ($rt) {
        $rt->get('/', [\App\Http\Controllers\api\StatusController::class, 'status_kerja']);
    });
});
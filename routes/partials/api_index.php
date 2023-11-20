<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('index')->group(function ($router) {
    $router->get('get', [\App\Http\Controllers\api\IndexInsController::class, 'index']);
});
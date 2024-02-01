<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('undangan')->group(function ($router) {
    $router->get('/me', [\App\Http\Controllers\api\UndanganController::class, 'me']);
});
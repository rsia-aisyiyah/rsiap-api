<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('pendidikan')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\api\PendidikanController::class, 'index']);
});
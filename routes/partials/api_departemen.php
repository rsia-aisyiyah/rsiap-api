<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('departemen')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\api\DepartemenController::class, 'index']);
});
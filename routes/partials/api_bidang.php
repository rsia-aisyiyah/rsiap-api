<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('bidang')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\api\BidangController::class, 'index']);
});
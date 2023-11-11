<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('bank')->group(function ($router) {
    $router->get('get', [\App\Http\Controllers\api\BankController::class, 'index']);
});
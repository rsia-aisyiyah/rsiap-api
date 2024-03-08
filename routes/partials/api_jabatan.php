<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('jabatan')->group(function ($router) {
    $router->get('/', [\App\Http\Controllers\api\JabatanController::class, 'index']);
    $router->get('jenjang', [\App\Http\Controllers\api\JabatanController::class, 'jenjang']);
});
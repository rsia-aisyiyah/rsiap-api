<?php 

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->prefix('surat')->group(function ($router) {
    $router->get('internal', [\App\Http\Controllers\RsiaSuratInternalController::class, 'index']);
});
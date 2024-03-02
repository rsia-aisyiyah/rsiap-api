<?php

use Illuminate\Support\Facades\Route;

Route::prefix('menu-epersonal')->group(function ($router) {
    $router->get('/', [App\Http\Controllers\v2\MenuEPersonal::class, 'index']);
});

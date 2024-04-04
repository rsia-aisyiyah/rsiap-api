<?php

use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;


Route::prefix('pasien')->as('api.v2.')->group(function () {
    Orion::resource('ranap', \App\Http\Controllers\v2\KamarInapController::class);
});

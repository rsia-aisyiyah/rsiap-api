<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/', function () {
    return isOk("API v2 is working");
});

Route::post('/e-klaim', [App\Http\Controllers\v2\WSEklaim::class, 'index']);


$files = scandir(__DIR__ . '/partials_v2');
foreach ($files as $file) {
    // if file is not a directory
    if (!is_dir(__DIR__ . '/partials_v2/' . $file)) {
        // require_once the file
        require_once __DIR__ . '/partials_v2/' . $file;
    }
}

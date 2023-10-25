<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SmtpController;
use App\Http\Controllers\PushNotification;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FileManagerController;
use App\Http\Controllers\api\JasaMedisController;
use App\Http\Controllers\PushNotificationPegawai;

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
    return isOk('API is running');
});

// Auth without middleware
Route::prefix('auth')->group(function ($router) {
    $router->post('login', [AuthController::class, 'login']);
    $router->post('room/login', [AuthController::class, 'roomLogin']);
});

// Auth
Route::middleware('jwt.verify')->prefix('auth')->group(function ($router) {
    $router->post('me', [AuthController::class, 'me']);
    $router->post('logout', [AuthController::class, 'logout']);
    $router->post('refresh', [AuthController::class, 'refresh']);
    $router->post('validate', [AuthController::class, 'validateToken']);

    // Room Auth
    $router->post('room/me', [AuthController::class, 'roomMe']);
    $router->post('room/logout', [AuthController::class, 'roomLogout']);
    $router->post('room/refresh', [AuthController::class, 'roomRefresh']);
    $router->post('room/validate', [AuthController::class, 'roomValidateToken']);
});

// Testing Purpose
Route::get('/test', [\App\Http\Controllers\TestController::class, 'index'])->middleware('jwt.verify');

// Email SMTP
Route::get('/smtp', [SmtpController::class, 'index']);
Route::get('/jasa-medis', [JasaMedisController::class, 'index']);
Route::get('/jasa-pelayanan', [JasaMedisController::class, 'jasaPelayanan']);

//File Manager 
Route::get('/file-manager', [FileManagerController::class, 'index']);

// Push Notification mobile
Route::post('/notification/send', [PushNotification::class, 'send']);


// Push Notification mobile
Route::post('/notification/send/pegawai', [PushNotificationPegawai::class, 'send']);

require_once 'partials/api_dokter.php';
require_once 'partials/api_pasien.php';
require_once 'partials/api_kunjungan.php';
require_once 'partials/api_jadwal_operasi.php';
require_once 'partials/api_pegawai.php';

require_once 'partials/api_farmasi.php';

require_once 'partials/api_monitoring.php';
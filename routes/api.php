<?php

use App\Http\Controllers\api\JasaMedisController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SmtpController;
use App\Http\Controllers\PushNotification;
use App\Http\Controllers\api\AuthController;

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
    Route::post('login', [AuthController::class, 'login']);
});

// Auth
Route::middleware('api')->prefix('auth')->group(function ($router) {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('validate', [AuthController::class, 'validateToken']);

    // Room Auth
    Route::post('room/me', [AuthController::class, 'roomMe']);
    Route::post('room/login', [AuthController::class, 'roomLogin']);
    Route::post('room/logout', [AuthController::class, 'roomLogout']);
    Route::post('room/refresh', [AuthController::class, 'roomRefresh']);
    Route::post('room/validate', [AuthController::class, 'roomValidateToken']);
});


// Email SMTP
Route::get('/smtp', [SmtpController::class, 'index']);

// Jasa Medis Dokter
Route::get('/jasa-medis', [JasaMedisController::class, 'index']);

// Push Notification mobile
Route::post('/notification/send', [PushNotification::class, 'send']);

require_once 'partials/api_dokter.php';
require_once 'partials/api_pasien.php';
require_once 'partials/api_kunjungan.php';
require_once 'partials/api_jadwal_operasi.php';
require_once 'partials/api_pegawai.php';

require_once 'partials/api_monitoring.php';
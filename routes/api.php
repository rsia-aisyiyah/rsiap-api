<?php

use App\Http\Controllers\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\dokter\DokterController;
use App\Http\Controllers\api\dokter\PasienController;
use App\Http\Controllers\api\dokter\KunjunganController;
use App\Http\Controllers\api\dokter\PasienRalanController;
use App\Http\Controllers\api\dokter\PasienRanapController;
use App\Http\Controllers\api\dokter\JadwalOperasiController;
use App\Http\Controllers\api\dokter\OperasiController;
use App\Http\Controllers\api\dokter\JasaMedisController;
use App\Http\Controllers\api\dokter\SmtpController;

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

// Push Notification mobile
Route::post('/notification/send', [PushNotification::class, 'send']);

// Auth without middleware
Route::prefix('auth')->group(function ($router) {
    Route::post('login', [AuthController::class, 'login']);
});

// Auth
Route::middleware('api')->prefix('auth')->group(function ($router) {
    Route::post('validate', [AuthController::class, 'validateToken']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
});

require_once 'partials/api_dokter.php';
require_once 'partials/api_pegawai.php';
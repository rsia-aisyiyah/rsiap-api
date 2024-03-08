<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gc', function () {
    $credentials = 'firebase_credentials.json';
    $factory     = (new Factory)->withServiceAccount(base_path($credentials));

    // Messaging with SDK
    $messaging = $factory->createMessaging();

    $message = CloudMessage::withTarget('topic', 'dokter')
        ->fromArray([
            'topic'         => 'dokter',
            'notification'  => [
                'title'     => 'RSIA Mobile Dokter',
                'body'      => 'mwehehehehe',
            ],
            // optional
            'data'         => [
                'key' => 'value',
            ],
        ]);

    // dd($message);

    // send
    $messaging->send($message);
});
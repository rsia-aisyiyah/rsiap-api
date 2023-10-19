<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestController extends Controller
{    
    function index(Request $req) {
        // Cara Dapat user payload

        // 1
        // $payload = $req->payload;
        // dd($payload->get('sub'));

        // 2
        // $payload = auth()->payload();
        // dd($payload->get('sub'));
    }
}

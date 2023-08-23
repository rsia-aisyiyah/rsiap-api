<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmtpController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $message = 'SMTP berhasil dimuat';
        $smtp  = \App\Models\Smtp::first();
        return isSuccess($smtp, $message);
    }

}

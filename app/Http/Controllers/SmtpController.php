<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmtpController extends Controller
{
    public function index()
    {
        $message = 'SMTP berhasil dimuat';
        $smtp  = \App\Models\Smtp::first();

        if (!$smtp) {
            $message = 'SMTP belum diatur';
        }

        return isSuccess($smtp, $message);
    }

}

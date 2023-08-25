<?php

namespace App\Http\Controllers\api\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = \App\Models\Pegawai::get();
        return isSuccess($pegawai);
    }
}

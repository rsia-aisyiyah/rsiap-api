<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexInsController extends Controller
{
    public function index(Request $request)
    {
        $bid = \App\Models\IndexIns::select('*');
        $bid = $bid->get();

        return isSuccess($bid, 'Data index berhasil ditemukan');
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index(Request $request)
    {
        $bid = \App\Models\Bidang::select('*');

        if ($request->q) {
            $bid = $bid->where('nama', 'like', '%' . $request->q . '%');
        }

        $bid = $bid->get();

        return isSuccess($bid, 'Data bidang berhasil ditemukan');
    }
}

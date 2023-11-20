<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function status_kerja(Request $request)
    {
        $bid = \App\Models\StatusKerja::select('*');

        if ($request->q) {
            $bid = $bid->where('ktg', 'like', '%' . $request->q . '%');
        }

        $bid = $bid->get();

        return isSuccess($bid, 'Data status kerja berhasil ditemukan');
    }
}

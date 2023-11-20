<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResikoController extends Controller
{
    public function resiko_kerja(Request $request)
    {
        $bid = \App\Models\ResikoKerja::select('*');

        if ($request->q) {
            $bid = $bid->where('nama_resiko', 'like', '%' . $request->q . '%');
        }

        $bid = $bid->get();

        return isSuccess($bid, 'Data resiko kerja berhasil ditemukan');
    }
}

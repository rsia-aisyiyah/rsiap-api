<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RsiaSuratInternalController extends Controller
{
    public function index(Request $request)
    {
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::select("*");

        $data = $rsia_surat_internal->get();

        return isSuccess($data, "Data berhasil ditemukan");
    }
}

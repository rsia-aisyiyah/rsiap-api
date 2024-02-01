<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UndanganController extends Controller
{
    public function me(Request $request)
    {
        $nip = $request->payload['sub'];
        $data = \App\Models\RsiaSuratInternalPenerima::select("*")
            ->with('surat')   
            ->where('penerima', $nip)
            ->paginate(env('PER_PAGE', 10));

        return isSuccess($data, "Berhasil mendapatkan data");
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UndanganController extends Controller
{
    public function index()
    {
        $data = \App\Models\RsiaSuratInternalPenerima::select("*")
            ->with(['surat' => function ($q) {
                $q->with(['penanggung_jawab' => function ($q) {
                    $q->select('nik', 'nama');
                }]);
            }, 'notulen' => function($q) {
                $q->select('no_surat', 'notulis_nik', 'created_at')->with(['notulis' => function ($q) {
                    $q->select('nik', 'nama');
                }]);
            }])
            ->orderBy('no_surat', 'DESC')
            ->groupBy('no_surat')
            ->paginate(env('PER_PAGE', 10));

        return isSuccess($data, "Berhasil mendapatkan data");
    }

    public function me(Request $request)
    {
        $nip = $request->payload['sub'];
        $data = \App\Models\RsiaSuratInternalPenerima::select("*")
            ->with('surat')   
            ->where('penerima', $nip)
            ->orderBy('no_surat', 'DESC')
            ->paginate(env('PER_PAGE', 10));

        return isSuccess($data, "Berhasil mendapatkan data");
    }
}

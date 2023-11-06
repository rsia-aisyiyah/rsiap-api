<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BerkasController extends Controller
{
    public function index()
    {
        $payload = auth()->payload();
        $nik = $payload->get('sub');

        $berkas = \App\Models\BerkasPegawai::with('master_berkas_pegawai')
            ->where('nik', $nik)
            ->get();

        if (!$berkas) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($berkas);
    }
}

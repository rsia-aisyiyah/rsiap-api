<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @authenticated
 * @group RSIA Kualifikasi Staff Klinis
 * 
 * data kualifikasi staff klinis, pada end points ini terdapat beberapa fitur seperti :
 * 1. detail kualifikasi staff klinis RSIA Aisyiyah Pekajangan
 */ 
class RsiaKlinisController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    /** @authenticated
     * Get Kualifikasi Staff Klinis
     * 
     * pada end points in data yang diperoleh adalah data pegawai dan data kualifikasi staff klinis, data kualifikasi staff klinis meliputi data kualifikasi staff klinis RSIA Aisyiyah Pekajangan.
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * @return \Illuminate\Http\Response
     *  
     * */ 
    public function index(Request $request)
    {
        $pegawai = \App\Models\Pegawai::with('kualifikasi_staff_klinis')
            ->where('nik', $request->nik)
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai);
    }
}
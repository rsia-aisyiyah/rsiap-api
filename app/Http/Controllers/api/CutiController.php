<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @authenticated
 * @group Cuti Pegawai
 * 
 * data cuti pegawai, pada end points ini terdapat beberapa fitur seperti :
 * 1. Riwayat Cuti Pegawai
 * 2. pengajuan cuti pegawai
 * 3. Pengajuan cuti bersalin
 * */ 
class CutiController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    /** @authenticated
     * Get Cuti Pegawai
     * 
     * pada end points in data yang diperoleh adalah data pegawai dan data cuti pegawai, data cuti pegawai meliputi data cuti bersalin (jika pegawai sedang cuti bersalin). cuti bersalin akan menghasilkan null jika pegawai tidak sedang cuti bersalin. 
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * */
    public function index(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::with('cuti.bersalin')
            ->where('nik', $request->nik)
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai);
    }
}
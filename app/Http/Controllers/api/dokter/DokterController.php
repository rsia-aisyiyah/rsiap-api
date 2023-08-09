<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        // get dokter by kd_dokter, kd_dokter get from token sub
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\Dokter::with(['pegawai', 'pegawai.kualifikasi_staff'])
            ->where('kd_dokter', $kd_dokter)
            ->first();

        return isSuccess($dokter, 'Data berhasil dimuat');
    }

    public function spesialis()
    {
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\Dokter::getSpesialis($kd_dokter);

        return isSuccess($dokter, 'Data berhasil dimuat');
    }
}

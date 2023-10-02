<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Jasa Medis
 * */
class JasaMedisController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\JasaMedis::with('pegawai')
            ->where('kd_dokter', $kd_dokter)
            ->orderBy('tahun','Desc')
            ->orderBy('bulan','Desc')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($dokter, 'Jasa medis dokter berhasil dimuat');
    }

    public function jasaPelayanan()
    {
        $nik = $this->payload->get('sub');
        $pegawai = \App\Models\JasaPelayanan::with('pegawai')
            ->where('nik', $nik)
            ->where('status_payroll', '1')
            ->orderBy('tahun','Desc')
            ->orderBy('bulan','Desc')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pegawai, 'Jasa pelayanan berhasil dimuat');
    }
}

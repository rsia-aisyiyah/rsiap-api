<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Dokter
 * */ 
class DokterController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\Dokter::with(['pegawai', 'pegawai.kualifikasi_staff_klinis', 'spesialis','pegawai.rsia_email_pegawai'])
            ->where('kd_dokter', $kd_dokter)
            ->first();

        return isSuccess($dokter, 'Dokter berhasil dimuat');
    }

    public function spesialis()
    {
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\Dokter::getSpesialis($kd_dokter);

        return isSuccess($dokter, 'Data Spesialis dokter berhasil dimuat');
    }
}

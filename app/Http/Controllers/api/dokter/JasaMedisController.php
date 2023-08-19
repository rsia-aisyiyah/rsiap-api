<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        return isSuccess($dokter, 'Data berhasil dimuat');
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Jasa Medis
 * */
class JasaMedisController extends Controller
{
    public function index()
    {
        $payload = auth()->payload();
        $kd_dokter = $payload->get('sub');
        
        $dokter = \App\Models\JasaMedis::with('pegawai')
            ->where('kd_dokter', $kd_dokter)
            ->orderBy('tahun','Desc')
            ->orderBy('bulan','Desc')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($dokter, 'Jasa medis dokter berhasil dimuat');
    }

    public function jasaPelayanan()
    {
        $payload = auth()->payload();
        $nik = $payload->get('sub');
        
        $pegawai = \App\Models\JasaPelayanan::with(['pegawai','jasa_pelayanan_akun'=>function($key){
            return $key->where('id_akun','12');
        }])
            ->where('nik', $nik)
            ->where('status_payroll', '1')
            ->orderBy('tahun','Desc')
            ->orderBy('bulan','Desc')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pegawai, 'Jasa pelayanan berhasil dimuat');
    }
}

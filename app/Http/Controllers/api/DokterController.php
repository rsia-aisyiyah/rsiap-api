<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Dokter
 * */ 
class DokterController extends Controller
{
    public function index()
    {
        $payload = auth()->payload();
        $kd_dokter = $payload->get('sub');
        
        $dokter = \App\Models\Dokter::with(['pegawai', 'pegawai.kualifikasi_staff_klinis', 'spesialis','pegawai.rsia_email_pegawai'])
            ->where('kd_dokter', $kd_dokter)
            ->first();

        return isSuccess($dokter, 'Dokter berhasil dimuat');
    }

    public function spesialis()
    {
        $payload = auth()->payload();
        $kd_dokter = $payload->get('sub');

        $dokter = \App\Models\Dokter::getSpesialis($kd_dokter);

        return isSuccess($dokter, 'Data Spesialis dokter berhasil dimuat');
    }

    public function getData(Request $request)
    {
        $dokter = \App\Models\Dokter::select('kd_sps', 'nm_dokter')
            ->with(['spesialis'])
            ->where('status', '1')
            ->where('nm_dokter', "<>", '-')
            ->where('kd_dokter', "<>", '-');

        if ($request->has('sps')) {
            $dokter->where('nm_sps', 'like', '%' . $request->sps . '%');
        }

        if ($request->has('nm_dokter')) {
            $dokter->where('nm_dokter', 'like', '%' . $request->nm_dokter . '%');
        }

        if ($request->has('paginate')) {
            $dokter = $dokter->paginate($request->paginate);
        } else {
            $dokter = $dokter->get();
        }

        return isSuccess($dokter, 'Dokter berhasil dimuat');
    }
}

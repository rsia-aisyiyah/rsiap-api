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
        $dokter = \App\Models\Dokter::select('kd_dokter', 'kd_sps', 'nm_dokter')
            ->with(['spesialis', 'pegawai' => function ($query) {
                return $query->select('nik', 'nama', 'jk', 'jbtn', 'photo');
            }])
            ->where('status', '1')
            ->where('nm_dokter', "<>", '-');

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

    public function getSpesialis(Request $request) 
    {
        $spesialis = \App\Models\Spesialis::get();

        return isSuccess($spesialis, 'Data Spesialis berhasil dimuat');
    }

    public function getJadwal(Request $request)
    {
        $jadwal = \App\Models\Dokter::select('kd_dokter', 'nm_dokter', 'kd_sps')->with(['jadwal' => function($q) {
            $q->where('kuota', '!=', 0);
        }, 'jadwal.poliklinik', 'spesialis'])->whereHas('jadwal');
        $msg = 'Data Jadwal dokter';

        if ($request->kd_dokter) {
            $msg .= ' berdasarkan kd_dokter ' . $request->kd_dokter;
            $jadwal->where('kd_dokter', $request->kd_dokter);
        }

        if ($request->kd_poli) {
            $msg .= ' berdasarkan kd_poli ' . $request->kd_poli;
            $jadwal->whereHas('jadwal', function ($query) use ($request) {
                $query->where('kd_poli', $request->kd_poli);
            });
        }

        $msg .= ' berhasil dimuat';
        $jadwal = $jadwal->get()->groupBy('kd_dokter');
        
        return isSuccess($jadwal, 'Data Jadwal berhasil dimuat');
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MonitorResumePasien extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }
    public function ranap(Request $request)
    {
        $ranap = \App\Models\ResumePasienRanap::whereIn('shk', ['ya', 'Ya', 'YA', 'tidak', 'Tidak', 'TIDAK', 'belum', 'Belum', 'BELUM', 'sudah', 'Sudah', 'SUDAH'])
            ->with([
                'regPeriksa'        => function ($q) {
                    $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'kd_dokter', 'kd_poli', 'tgl_registrasi', 'status_lanjut');
                },
                'regPeriksa.pasien' => function ($query) {
                    $query->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'pekerjaan', 'alamat', 'no_tlp');
                },
            ])->orderBy('no_rawat', 'DESC');

        if ($request->datatables == 'true') {
            $ranap = $ranap->get();
            return DataTables::of($ranap)->make(true);
        }

        $ranap = $ranap->paginate(env('PER_PAGE', 20));

        return isSuccess($ranap, 'Data berhasil dimuat');
    }
}
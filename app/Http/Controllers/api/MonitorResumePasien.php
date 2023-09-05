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
        $ranap = \App\Models\ResumePasienRanap::whereIn('shk', ['belum', 'Belum', 'BELUM', 'sudah', 'Sudah', 'SUDAH'])
            ->with([
                'regPeriksa'        => function ($q) {
                    $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'kd_dokter', 'kd_poli', 'tgl_registrasi', 'status_lanjut', 'p_jawab');
                },
                'regPeriksa.pasien' => function ($query) {
                    $query->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'pekerjaan', 'alamat', 'no_tlp');
                },
                'regPeriksa.penjab' => function ($query) {
                    $query->select('kd_pj', 'png_jawab');
                },
                'dokter'            => function ($query) {
                    $query->select('kd_dokter', 'nm_dokter');
                },
            ])->orderBy('no_rawat', 'DESC');

        if ($request->tgl_registrasi) {
            if ($request->tgl_registrasi['start'] != null && $request->tgl_registrasi['end'] != null) {
                $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
                $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');

                $ranap = $ranap->whereHas('regPeriksa', function ($query) use ($start, $end) {
                    $query->whereBetween('tgl_registrasi', [$start, $end]);
                });
            }
        }

        if ($request->shk && $request->shk != null && $request->shk != 'all') {
            $ranap = $ranap->where('shk', $request->shk);
        }

        if ($request->pembiayaan && $request->pembiayaan != null && $request->pembiayaan != 'all') {
            $ranap->whereHas('regPeriksa.penjab', function ($query) use ($request) {
                $query->where('png_jawab', 'LIKE', '%' . $request->pembiayaan . '%');
            });
        }

        if ($request->datatables == 'true') {
            $ranap = $ranap->get();
            return DataTables::of($ranap)->make(true);
        }

        $ranap = $ranap->paginate(env('PER_PAGE', 20));

        return isSuccess($ranap, 'Data berhasil dimuat');
    }
}
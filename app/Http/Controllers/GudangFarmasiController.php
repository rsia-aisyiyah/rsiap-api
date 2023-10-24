<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GudangFarmasiController extends Controller
{
    public function index()
    {
        return isSuccess('Endpoint untuk gudang farmasi');
    }

    public function metrics(Request $request)
    {
        $msg = "Data detail pemberian obat";
        $data = \App\Models\DetailPemberianObat::select('status')
            ->selectRaw("SUM(total) AS total");

        if ($request->tgl_perawatan) {
            $msg .= " bulan {$request->bulan}";
            $data = $data->whereMonth('tgl_perawatan', $request->tgl_perawatan['bulan'])->whereYear('tgl_perawatan', $request->tgl_perawatan['tahun']);
        } else {
            $msg .= " bulan ini ";
            $data = $data->whereMonth('tgl_perawatan', date('m'))->whereYear('tgl_perawatan', date('Y'));
        }

        $data = $data->groupBy('status')->get();

        $msg .= " berhasil diambil";
        return isSuccess($data, $msg);
    }

    public function topObat(Request $request)
    {
        $msg = "Data detail obat";
        $data = \App\Models\DetailPemberianObat::select('kode_brng')
        ->selectRaw('SUM(jml) AS total')
        ->with(['obat' => function ($q) {
            $q->select('nama_brng', 'kode_brng');
        }]);

        if ($request->tgl_perawatan) {
            $msg .= " bulan {$request->tgl_perawatan['bulan']} {$request->tgl_perawatan['tahun']}";
            $data = $data->whereMonth('tgl_perawatan', $request->tgl_perawatan['bulan'])->whereYear('tgl_perawatan', $request->tgl_perawatan['tahun']);
        } else {
            $msg .= " bulan ini ";
            $data = $data->whereMonth('tgl_perawatan', date('m'))->whereYear('tgl_perawatan', date('Y'));
        }

        $data = $data->groupBy('kode_brng')->orderBy('total', 'DESC')->limit(10)->get();

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $data->map(function ($item) {
                    return [
                        'kode_obat' => $item->kode_brng,
                        'nama_obat' => $item->obat->nama_brng,
                        'total' => $item->total,
                    ];
                });
                
                return DataTables::of($data)->make(true);
            } else {
                return isSuccess($data, $msg);
            }
        } else {
            return isSuccess($data, $msg);
        }
    }

    public function metricsDetail(Request $request)
    {
        $msg = "Data detail pemberian obat";
        $data = \App\Models\DetailPemberianObat::select('tgl_perawatan')
            ->selectRaw('COUNT(CASE WHEN status = "ralan" THEN no_rawat END) AS count_no_rawat_ralan')
            ->selectRaw('COUNT(CASE WHEN status = "ranap" THEN no_rawat END) AS count_no_rawat_ranap');

        if ($request->tgl_perawatan) {
            $msg .= " bulan {$request->bulan}";
            $data = $data->whereMonth('tgl_perawatan', $request->tgl_perawatan['bulan'])->whereYear('tgl_perawatan', $request->tgl_perawatan['tahun']);
        } else {
            $msg .= " bulan ini ";
            $data = $data->whereMonth('tgl_perawatan', date('m'))->whereYear('tgl_perawatan', date('Y'));
        }

        $data = $data->groupBy('tgl_perawatan')->get();

        $msg .= " berhasil diambil";
        return isSuccess($data, $msg);
    }
}

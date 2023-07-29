<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OperasiController extends Controller
{

    protected $payload;

    function __construct()
    {
        $this->payload = auth()->payload();
    }

    function index()
    {
        $message = 'Seluruh Pasien Operasi berhasil dimuat';
        $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab'])
            ->whereHas('operasi')
            ->where('kd_dokter', $this->payload->get('sub'))
            ->orderBy('no_rawat', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }

    function data(Request $request)
    {
        if (!$request->isMethod('post')) {
            return isFail('Method not allowed');
        }

        if (!$request->has('no_rawat')) {
            return isFail('No Rawat tidak ditemukan');
        }

        $operasi = \App\Models\Operasi::with('paketOperasi')
            ->where('no_rawat', $request->no_rawat)
            ->get();

        return isSuccess($operasi, 'Data Operasi berhasil dimuat');
    }

    function filter(Request $request)
    {
        $message = 'Seluruh Pasien Operasi';
        $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab'])
            ->where('kd_dokter', $this->payload->get('sub'))
            ->whereHas('operasi');

        if ($request->keywords) {
            $pasien->whereHas('pasien', function ($query) use ($request) {
                $query->where('nm_pasien', 'LIKE', '%' . $request->keywords . '%');
            })
            ->orWhere('no_rawat', 'LIKE', '%' . $request->keywords . '%')
            ->orWhere('no_rkm_medis', 'LIKE', '%' . $request->keywords . '%');
        }

        if ($request->penjab) {
            $message .= ' dengan penjab ' . $request->penjab;
            $pasien->whereHas('penjab', function ($query) use ($request) {
                $query->where('png_jawab', 'LIKE', '%' . $request->penjab . '%');
            });
        }

        // if ($request->tgl_operasi) {
        //     $start = Carbon::parse($request->tgl_operasi['start'])->format('Y-m-d');
        //     $end   = Carbon::parse($request->tgl_operasi['end'])->format('Y-m-d');

        //     $message .= ' dari tanggal ' . $start . ' sampai ' . $end;

        //     $pasien->whereHas('operasi', function ($query) use ($start, $end) {
        //         $query->whereBetween('tgl_operasi', [$start, $end]);
        //     });
        // }

        $pasien = $pasien->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }
}
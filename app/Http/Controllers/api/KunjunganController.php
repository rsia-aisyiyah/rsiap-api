<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Kunjungan Dokter
 * */
class KunjunganController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $kd_dokter = $this->payload->get('sub');
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }

    public function now()
    {
        $kd_dokter = $this->payload->get('sub');
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }

    private function getTotal($source)
    {
        $dataMap = ["UMUM", "BPJS", "TOTAL"];
        $data    = $source->pluck('penjab')->countBy(
            function ($item, $key) {
                return str_contains($item['png_jawab'], "BPJS") ? 'BPJS' : $item['png_jawab'];
            }
        );

        foreach ($dataMap as $key => $value) {
            if (!isset($data[$value])) {
                $data[$value] = 0;
            }

            if ($value == "TOTAL") {
                $data[$value] = $data['UMUM'] + $data['BPJS'];
            }
        }

        return $data;
    }

    function rekap(Request $request)
    {
        if (!$request->isMethod('post')) {
            return isFail('Method not allowed');
        }

        $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab'])
            ->where('kd_dokter', $this->payload->get('sub'))
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC');

        $operasi = \App\Models\RegPeriksa::with(['pasien', 'penjab'])
            ->where('kd_dokter', $this->payload->get('sub'))
            ->whereHas('operasi')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC');

        $start = date('Y-m-01');
        $end   = date('Y-m-t');

        if ($request->tgl_registrasi) {
            $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
            $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');
        }

        $pasien->whereBetween('tgl_registrasi', [$start, $end]);
        $operasi->whereBetween('tgl_registrasi', [$start, $end]);

        $data            = $pasien->get()->groupBy('status_lanjut')->map(function ($item, $key) {
            return $this->getTotal(collect($item));
        });
        $data['Operasi'] = $this->getTotal($operasi->get());

        return isSuccess($this->checkData($data), 'Data berhasil dimuat');
    }
    
    function rekapUmum(Request $request)
    {

        if (!$request->isMethod('post')) {
            return isFail('Method not allowed');
        }

        if ($request->tgl_registrasi) {
            $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
            $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');
        } else {
            $start = date('Y-m-01');
            $end   = date('Y-m-t');
        }

        $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab'])
            ->whereHas('ranapDokter', function ($query) use ($start, $end) {
                $query->where('kd_dokter', $this->payload->get('sub'))->whereBetween('tgl_perawatan', [$start, $end]);
            })
            ->orWhereHas('ralanDokter', function ($query) use ($start, $end) {
                $query->where('kd_dokter', $this->payload->get('sub'))->whereBetween('tgl_perawatan', [$start, $end]);
            })
            ->orWhereHas('ranapGabungan', function ($query) use ($start, $end) {
                $query->where('kd_dokter', $this->payload->get('sub'))->whereBetween('tgl_perawatan', [$start, $end]);
            })
            ->orWhereHas('ralanGabungan', function ($query) use ($start, $end) {
                $query->where('kd_dokter', $this->payload->get('sub'))->whereBetween('tgl_perawatan', [$start, $end]);
            })
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC');

        $data = $pasien->get()->groupBy('status_lanjut')->map(function ($item, $key) {
            return $this->getTotal(collect($item));
        });

        return isSuccess($this->checkData($data), "Data rekap pasen dokter umum pada $start - $end berhasil dimuat");
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'));
        if ($tahun !== null) {
            $kunjungan->whereYear('tgl_registrasi', $tahun);
        }

        if ($tahun !== null && $bulan !== null) {
            $kunjungan->whereYear('tgl_registrasi', $tahun)->whereMonth('tgl_registrasi', $bulan);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $kunjungan->where('tgl_registrasi', $fullDate);
        }

        $kunjungan = $kunjungan->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')->paginate(env('PER_PAGE', 20));

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }

    private function checkData($data, $isUmum = false)
    {
        $dataVal = ["UMUM", "BPJS", "TOTAL"];
        $dataKey = ["Ranap", "Ralan", "Operasi"];

        // jika dataKey didalam data tidak adan maka tambahkan dataKey dengan isi dataVal
        foreach ($dataKey as $key => $value) {
            if (!isset($data[$value])) {
                $data[$value] = array_fill_keys($dataVal, 0);
            }
        }

        $data = collect($data)->sortBy(function ($item, $key) use ($dataKey) {
            return array_search($key, $dataKey);
        })->toArray();

        return $data;
    }
}
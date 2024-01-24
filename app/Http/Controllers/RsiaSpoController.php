<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RsiaSpoController extends Controller
{
    public function index(Request $request)
    {
        $rsia_spo = \App\Models\RsiaSpo::with([
            "departemen",
            "detail" => function ($q) {
                $q->select('nomor');
            }
        ])->select("*")->where('status', '1');

        if ($request->keyword) {
            $rsia_spo = $rsia_spo->where('status', '1')->where('judul', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('unit', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('nomor', 'LIKE', '%' . $request->keyword . '%');
        }

        // tgl_terbit
        if ($request->tgl_terbit) {
            $rsia_spo = $rsia_spo->where('status', '1')->where('tgl_terbit', $request->tgl_terbit);
        }

        if ($request->jenis) {
            $jenis = $request->jenis;
            if (substr($jenis, 0, 1) != '/') {
                $jenis = '/' . $jenis;
            }

            if (substr($jenis, -1) != '/') {
                $jenis = $jenis . '/';
            }

            $rsia_spo = $rsia_spo->where('status', '1')->where('nomor', 'LIKE', '%' . $jenis . '%');
        }

        $rsia_spo = $rsia_spo->where('status', '1')->orderBy('nomor', 'desc');

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $rsia_spo->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $rsia_spo->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $rsia_spo->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, 'SPO berhasil ditampilkan');
    }

    public function show(Request $request)
    {
        if (!$request->nomor) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $id = $request->nomor;
        $id = str_replace('_', '/', $id);
        $rsia_spo = \App\Models\RsiaSpo::select("*")->with('detail', 'departemen')->where('nomor', $id)->first();

        if (!$rsia_spo) {
            return isFail('SPO tidak ditemukan', 404);
        }

        return isSuccess($rsia_spo, 'Detail SPO berhasil ditampilkan');
    }

    public function store(Request $request)
    {
        $rules = [
            "nomor" => "required",
            "judul" => "required",
            "unit" => "required",
            "tgl_terbit" => "required",
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $rsia_spo = \App\Models\RsiaSpo::create($request->except('payload'));
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            return isFail($th->getMessage(), 500);
        }
        \Illuminate\Support\Facades\DB::commit();

        return isSuccess($rsia_spo, 'SPO berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        if (!$request->nomor) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $id = $request->nomor;
        $id = str_replace('_', '/', $id);
        $rsia_spo = \App\Models\RsiaSpo::select("*")->where('nomor', $id)->first();

        if (!$rsia_spo) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $rules = [
            "nomor" => "required",
            "judul" => "required",
            "unit" => "required",
            "tgl_terbit" => "required",
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $rsia_spo->update($request->except('payload'));
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            return isFail($th->getMessage(), 500);
        }
        \Illuminate\Support\Facades\DB::commit();

        return isSuccess($rsia_spo, 'SPO berhasil diupdate');
    }

    public function delete(Request $request)
    {
        if (!$request->nomor) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $id = $request->nomor;
        $id = str_replace('_', '/', $id);
        $rsia_spo = \App\Models\RsiaSpo::select("*")->where('nomor', $id)->first();

        if (!$rsia_spo) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $rsia_spo->update(['status' => '0']);

        return isSuccess($rsia_spo, 'SPO berhasil dihapus');
    }

    public function destroy(Request $request)
    {
        if (!$request->nomor) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $id = $request->nomor;
        $rsia_spo = \App\Models\RsiaSpo::select("*")->where('nomor', $id)->first();

        if (!$rsia_spo) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $rsia_spo->delete();

        return isSuccess(null, 'SPO berhasil dihapus');
    }

    public function getLastNomor(Request $request)
    {
        $data = [
            'medis' => \App\Models\RsiaSpo::select('nomor')->whereYear('tgl_terbit', date('Y'))->where('nomor', 'LIKE', '%/A/%')->orderBy('nomor', 'desc')->first()->nomor,
            'penunjang' => \App\Models\RsiaSpo::select('nomor')->whereYear('tgl_terbit', date('Y'))->where('nomor', 'LIKE', '%/B/%')->orderBy('nomor', 'desc')->first()->nomor,
            'umum' => \App\Models\RsiaSpo::select('nomor')->whereYear('tgl_terbit', date('Y'))->where('nomor', 'LIKE', '%/C/%')->orderBy('nomor', 'desc')->first()->nomor,
        ];

        return isSuccess($data, 'Data SPO berhasil ditampilkan');
    }

    public function renderPdf($nomor)
    {
        $rn = str_replace('--', '/', $nomor);
        $rsia_spo = \App\Models\RsiaSpo::select("*")->with('detail')->where('nomor', $rn)->first();

        if (!$rsia_spo) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $spo = $rsia_spo;
        $detail = [];

        foreach ($spo->detail->toArray() as $key => $value) {
            if ($key != 'nomor') {
                $detail[$key] = html_entity_decode($value);
            }
        }

        // return view ('print.spo', compact('spo'));
        $html = view('print.spo', compact('spo', 'detail'))->render();

        $pdf = PDF::loadHtml($html)->setPaper('a4', 'portrait')->setWarnings(false)->setOptions([
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'isFontSubsettingEnabled' => true,
            'isJavascriptEnabled' => true,
        ]);

        // return $pdf->stream('spo.pdf');

        $filename = strtoupper(str_replace(' ', '_', $spo->judul) . '_SPO') . '.pdf';
        return $pdf->download($filename);
    }
}

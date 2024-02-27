<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RsiaBerkasPMKP extends Controller
{
    public function index(Request $request)
    {
        $rsia_surat_ppi = \App\Models\RsiaBerkasPMKP::with(['penanggungjawab' => function ($q) {
            $q->select('nik', 'nama', 'jbtn');
        }])->where('status', '1');

        if ($request->keyword) {
            $rsia_surat_ppi->where('nomor', 'like', '%' . $request->keyword . '%')
                ->orWhere('perihal', 'like', '%' . $request->keyword . '%')
                ->orWhere('pj', 'like', '%' . $request->keyword . '%')
                ->orWhereHas('penanggungjawab', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->keyword . '%');
                });
        }

        if ($request->sort) {
            $rsia_surat_ppi->orderBy($request->sort, $request->order);
        } else {
            $rsia_surat_ppi->orderBy('created_at', 'desc');
        }

        if ($request->data) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $rsia_surat_ppi->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $rsia_surat_ppi->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $rsia_surat_ppi->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, 'Data berhasil ditemukan');
    }

    public function show(Request $request)
    {
        if (!$request->nomor || !$request->tgl_terbit) {
            return isFail('Data tidak ditemukan');
        }

        $rsia_surat_ppi = \App\Models\RsiaBerkasPMKP::with('penanggungjawab')->where('nomor', $request->nomor)->where('tgl_terbit', $request->tgl_terbit)->first();

        if (!$rsia_surat_ppi) {
            return isFail('Data tidak ditemukan');
        }

        return isSuccess($rsia_surat_ppi, 'Data berhasil ditemukan');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'pj' => 'required|exists:pegawai,nik',
            'perihal' => 'required',
            'tgl_terbit' => 'required|date',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors());
        }

        $rsia_surat_ppi = new \App\Models\RsiaBerkasPMKP();
        $rsia_surat_ppi->nomor = $this->getLastNomor($request->tgl_terbit);
        $rsia_surat_ppi->pj = $request->pj;
        $rsia_surat_ppi->perihal = $request->perihal;
        $rsia_surat_ppi->tgl_terbit = $request->tgl_terbit;

        if (!$rsia_surat_ppi->save()) {
            return isFail('Data gagal disimpan');
        }

        return isSuccess($rsia_surat_ppi, 'Data berhasil disimpan');
    }

    public function update(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor' => 'required',
            'tgl_terbit' => 'required',

            'pj' => 'required|exists:pegawai,nik',
            'perihal' => 'required',
            'tgl_terbit' => 'required|date',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors());
        }

        $rsia_surat_ppi = \App\Models\RsiaBerkasPMKP::where('nomor', $request->nomor)->where('tgl_terbit', $request->tgl_terbit)->first();

        if (!$rsia_surat_ppi) {
            return isFail('Data tidak ditemukan');
        }

        $rsia_surat_ppi->pj = $request->pj;
        $rsia_surat_ppi->perihal = $request->perihal;
        
        if (!$rsia_surat_ppi->save()) {
            return isFail('Data gagal disimpan');
        }

        return isSuccess($rsia_surat_ppi, 'Data berhasil disimpan');
    }

    // delete status changr to 0
    public function delete(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor' => 'required',
            'tgl_terbit' => 'required'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors());
        }

        $rsia_surat_ppi = \App\Models\RsiaBerkasPMKP::where('nomor', $request->nomor)->where('tgl_terbit', $request->tgl_terbit)->first();

        if (!$rsia_surat_ppi) {
            return isFail('Data tidak ditemukan');
        }

        $rsia_surat_ppi->status = 0;
        if (!$rsia_surat_ppi->save()) {
            return isFail('Data gagal dihapus');
        }

        return isSuccess($rsia_surat_ppi, 'Data berhasil dihapus');
    }

    // destroy data from database
    public function destroy(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor' => 'required',
            'tgl_terbit' => 'required'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors());
        }

        $rsia_surat_ppi = \App\Models\RsiaBerkasPMKP::where('nomor', $request->nomor)->where('tgl_terbit', $request->tgl_terbit)->first();

        if (!$rsia_surat_ppi) {
            return isFail('Data tidak ditemukan');
        }

        if (!$rsia_surat_ppi->delete()) {
            return isFail('Data gagal dihapus');
        }

        return isSuccess($rsia_surat_ppi, 'Data berhasil dihapus');
    }   

    protected function getLastNomor($tgl_terbit)
    {
        $year = date('Y', strtotime($tgl_terbit));
        $rsia_surat_ppi = \App\Models\RsiaBerkasPMKP::whereYear('tgl_terbit', $year)->max('nomor');

        if (!$rsia_surat_ppi) {
            return 1;
        }

        return $rsia_surat_ppi + 1;
    }
}

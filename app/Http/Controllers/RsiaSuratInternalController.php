<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RsiaSuratInternalController extends Controller
{
    public function index(Request $request)
    {
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::select("*")->with(['pj_detail' => function ($q) {
            $q->select('nip', 'nama');
        }]);
        $data = $rsia_surat_internal->orderBy('no_surat', 'desc');

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $data->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $data->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $data->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, "Data berhasil ditemukan");
    }

    public function get_by(Request $request)
    {
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::with(['pj_detail' => function ($q) {
            $q->select('nip', 'nama');
        }]);
        $data = $this->selSuratInternal($rsia_surat_internal, $request);
        $data = $this->colSuratInternal($rsia_surat_internal, $request);

        if ($request->group) {
            if (in_array($request->group, ['no_surat', 'penerima', 'pj', 'status'])) {
                $data = $data->orderBy('no_surat', 'desc')->get()->groupBy($request->group);
            } else {
                $data = $data->orderBy('no_surat', 'desc')->get();
            }
        } else {
            $data = $data->orderBy('no_surat', 'desc')->get();
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            }
        }

        return isSuccess($data, "Data berhasil ditemukan");
    }

    public function create(Request $request) 
    {
        // get last surat by nomor surat
        $data = \App\Models\RsiaSuratInternal::select('no_surat')->orderBy('no_surat', 'desc')->first();
        $data = explode('/', $data->no_surat);

        if (!$data) {
            return isFail("Problem to get last data");
        }

        // last number
        $date_now = date('dmy');
        $last_number = $data[0];
        $last_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        $nomor_surat = $last_number . '/A/S-RSIA/' . $date_now;

        // check request
        if (!$request->perihal) {
            return isFail("Perihal tidak boleh kosong");
        }

        if (!$request->pj) {
            return isFail("PJ tidak boleh kosong");
        }

        if (!$request->tanggal) {
            return isFail("Tanggal tidak boleh kosong");
        }
        
        if (!$request->tempat) {
            return isFail("Tempat tidak boleh kosong");
        }
        

        // insert data
        $rsia_surat_internal = new \App\Models\RsiaSuratInternal;
        $rsia_surat_internal->no_surat = $nomor_surat;
        $rsia_surat_internal->perihal = $request->perihal;
        $rsia_surat_internal->tempat = $request->tempat;
        $rsia_surat_internal->pj = $request->pj;
        $rsia_surat_internal->tanggal = $request->tanggal;
        $rsia_surat_internal->status = 'pengajuan';

        $rsia_surat_internal->save();

        return isSuccess([
            'no_surat' => $nomor_surat,
            'surat' => $rsia_surat_internal->toArray()
        ], "Surat berhasil dibuat");
    }

    public function update(Request $request)
    {
        if (!$request->no_surat) {
            return isFail("No surat tidak boleh kosong");
        }

        $rsia_surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat);
        $data = $rsia_surat_internal->update($request->except(['no_surat', 'payload']));

        return isSuccess($data, "Data berhasil diupdate");
    }

    public function destroy(Request $request)
    {
        if (!$request->no_surat) {
            return isFail("No surat tidak boleh kosong");
        }

        $rsia_surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat);
        $data = $rsia_surat_internal->delete();

        return isSuccess($data, "Data berhasil dihapus");
    }

    private function colSuratInternal($model, $request)
    {
        $col = ['no_surat', 'penerima', 'pj', 'status', 'month(tanggal)', 'year(tanggal)', 'date(tanggal)'];

        $new_model = $model->where(function ($q) use ($col, $request) {
            foreach ($col as $key => $value) {
                if ($request->has($value)) {
                    if ($value == 'month(tanggal)' || $value == 'year(tanggal)' || $value == 'date(tanggal)') {
                        $q->whereRaw($value . ' = ?', [$request->input($value)]);
                    } else {
                        $q->where($value, $request->input($value));
                    }
                }
            }
        });

        return $new_model;
    }

    private function selSuratInternal($modal, $request)
    {
        if ($request->select) {
            $select = explode(',', $request->select);
            $modal = $modal->select($select);
        } else {
            $modal = $modal->select('*');
        }

        return $modal;
    }
}

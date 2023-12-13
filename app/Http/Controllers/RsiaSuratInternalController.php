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

        if ($request->keyword) {
            $data = $data->where(function ($q) use ($request) {
                $q->where('no_surat', 'like', '%' . $request->keyword . '%')
                    ->orWhere('perihal', 'like', '%' . $request->keyword . '%')
                    ->orWhere('tempat', 'like', '%' . $request->keyword . '%')
                    ->orWhere('pj', 'like', '%' . $request->keyword . '%')
                    ->orWhereHas('pj_detail', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->keyword . '%');
                    });
            });
        }

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

    public function getCalendar(Request $request)
    {
        // get this month and  return [title is perihal, date is tanggal]
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::select('no_surat', 'tempat', 'pj', 'perihal as title', 'tanggal as date', 'tanggal', 'status')->with('pj_detail');

        if ($request->month && $request->year) {
            $rsia_surat_internal = $rsia_surat_internal->whereMonth('tanggal', $request->month)->whereYear('tanggal', $request->year);
        } else {
            $rsia_surat_internal = $rsia_surat_internal->whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'));
        }

        $rsia_surat_internal = $rsia_surat_internal->get();

        return isSuccess($rsia_surat_internal, "Data berhasil ditemukan");
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

    public function detail(Request $request)
    {
        if (!$request->nomor) {
            return isFail("No surat tidak boleh kosong");
        }

        $surat = \App\Models\RsiaSuratInternal::where('no_surat', $request->nomor)->with(['pj_detail' => function ($q) {
            $q->select('nip', 'nama');
        }])->first();

        if (!$surat) {
            return isFail("Data tidak ditemukan");
        }

        $surat->penerima = \App\Models\RsiaSuratInternalPenerima::where('no_surat', $request->nomor)->with(['pegawai' => function ($q) {
            $q->select('nik', 'nama', 'jbtn', 'bidang');
        }])->get();

        return isSuccess($surat, "Data berhasil ditemukan");
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

        // if (!$request->karyawan) {
        //     return isFail("Penerima tidak boleh kosong");
        // }

        try {
            // Start a database transaction
            \Illuminate\Support\Facades\DB::beginTransaction();

            $rsia_surat_internal = \App\Models\RsiaSuratInternal::create([
                'no_surat' => $nomor_surat,
                'perihal' => $request->perihal,
                'tempat' => $request->tempat,
                'pj' => $request->pj,
                'tanggal' => $request->tanggal,
                'status' => 'pengajuan',
            ]);

            $penerima = $request->karyawan ? $request->karyawan : [];
            foreach ($penerima as $key => $value) {
                $rsia_surat_internal_penerima = new \App\Models\RsiaSuratInternalPenerima;
                $rsia_surat_internal_penerima->no_surat = $nomor_surat;
                $rsia_surat_internal_penerima->penerima = $value;

                $rsia_surat_internal_penerima->save();
            }

            // Commit the transaction if everything is successful
            \Illuminate\Support\Facades\DB::commit();

            return isSuccess([
                'no_surat' => $nomor_surat,
                'surat' => $rsia_surat_internal->toArray()
            ], "Surat berhasil dibuat");
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            \Illuminate\Support\Facades\DB::rollback();

            return isFail("Error: " . $e->getMessage());
        }



        return isSuccess([
            'no_surat' => $nomor_surat,
            'surat' => $rsia_surat_internal->toArray()
        ], "Surat berhasil dibuat");
    }

    public function update(Request $request)
    {
        if (!$request->nomor) {
            return isFail("No surat tidak boleh kosong");
        }

        $update_data = [
            'pj' => $request->pj,
            'perihal' => $request->perihal,
            'tempat' => $request->tempat,
            'tanggal' => $request->tanggal,
        ];

        // Delete all penerima
        $rsia_surat_internal_penerima = \App\Models\RsiaSuratInternalPenerima::where('no_surat', $request->nomor);
        $rsia_surat_internal_penerima->delete();

        // Insert new penerima
        $penerima = $request->penerima ? $request->penerima : [];
        foreach ($penerima as $key => $value) {
            $rsia_surat_internal_penerima = new \App\Models\RsiaSuratInternalPenerima;
            $rsia_surat_internal_penerima->no_surat = $request->nomor;
            $rsia_surat_internal_penerima->penerima = $value;

            $rsia_surat_internal_penerima->save();
        }

        // Update the main record
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->nomor);
        $data = $rsia_surat_internal->update($update_data);

        // Update the PJ record
        // $rsia_surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->nomor);
        // $data = $rsia_surat_internal->update($update_pj);

        return isSuccess($data, "Data berhasil diupdate");
    }

    public function update_status(Request $request)
    {
        if (!$request->nomor) {
            return isFail("No surat tidak boleh kosong");
        }

        if (!$request->status) {
            return isFail("Status tidak boleh kosong");
        }

        $rsia_surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->nomor);
        $data = $rsia_surat_internal->update([
            'status' => $request->status
        ]);

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

    // metrics
    public function metrics(Request $request)
    {
        // get count all data group by status
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'));
        $data = $rsia_surat_internal->groupBy('status')->get();

        return isSuccess($data, "Data berhasil ditemukan");
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

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MemoInternalController extends Controller
{
    public function index(Request $request)
    {
        $memo = \App\Models\RsiaMemoInternal::with('perihal', 'perihal.pj_detail')->where('status', '1')->orderBy('no_surat', 'desc');

        if ($request->keyword) {
            $memo = $memo->where('no_surat', 'like', '%' . $request->keyword . '%')
                ->orWhere('mengetahui', 'like', '%' . $request->keyword . '%')
                ->orWhere('dari', 'like', '%' . $request->keyword . '%')
                ->orWhereHas('perihal', function ($query) use ($request) {
                    $query->where('perihal', 'like', '%' . $request->keyword . '%');
                });
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $memo->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $memo->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $memo->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, "Memo berhasil ditemukan");
    }

    public function show(Request $request, $nomor)
    {
        $nomor = str_replace('--', '/', $nomor);

        $memo = \App\Models\RsiaMemoInternal::with(['perihal', 'penerima', 'perihal.pj_detail'])->where('no_surat', $nomor)->first();

        if (!$memo) {
            return isFail("Memo tidak ditemukan");
        }

        return isSuccess($memo, "Memo berhasil ditemukan");
    }

    public function getPm(Request $request)
    {
        if (!$request->no_surat) {
            return isFail("Nomor surat tidak boleh kosong");
        }

        $mengetahui = [];
        $penerima = \App\Models\RsiaPenerimaUndangan::with(['pegawai' => function ($query) {
            $query->select('nik', 'nama');
        }])->where('no_surat', $request->no_surat)->get();
        

        $memo = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->first();
        
        if (!$memo) {
            $mengetahui = [];
        } else {
            $plainMengetahui = explode('|', $memo->mengetahui);
            $mengetahui = \App\Models\Pegawai::with(['jenjang_jabatan'])->select('nik', 'nama', 'jbtn', 'jnj_jabatan')->whereIn('nik', $plainMengetahui)->get();
        }

        return isSuccess([
            'mengetahui' => $mengetahui,
            'penerima' => $penerima,
        ], "Data berhasil ditemukan");
    }

    public function store(Request $request)
    {
        $rules = [
            'dari'          => 'required',
            'perihal'       => 'required',
            'pj'            => 'required',
            'tanggal'       => 'required|date_format:Y-m-d',
            'content'       => 'required',
            'mengetahui'    => 'required',
            'penerima'      => 'required',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors()->all());
        }

        $nomor_surat = $this->generateNomor($request);

        $data_surat_internal = [
            'no_surat'      => $nomor_surat,
            'perihal'       => $request->perihal,
            'tgl_terbit'    => $request->tanggal,

            'tempat'        => '-',
            'pj'            => $request->pj,
            'tanggal'       => '0000-00-00',
            'status'        => null,
        ];

        // mengetahui => nip petugas, bisa lebih dari satu dan dipisahkan dengan pipe (|)
        $data_memo_internal = [
            'dari'          => $request->dari,
            'no_surat'      => $nomor_surat,
            'content'       => $request->content,
            'mengetahui'    => $request->mengetahui,
        ];

        // penerima is array from post
        $penerima = $request->penerima ?? [];

        // penerima is json stringified, so we need to decode it first
        $penerima = json_decode($penerima);

        // db transaction for rollback if error for 2 table (rsia_surat_internal and rsia_memo_internal)
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // insert to rsia_surat_internal
            $surat_internal = \App\Models\RsiaSuratInternal::create($data_surat_internal);

            // insert to rsia_memo_internal
            $memo_internal = \App\Models\RsiaMemoInternal::create($data_memo_internal);
            
            // insert to rsia_penerima_undangan
            foreach ($penerima as $nip) {
                \App\Models\RsiaPenerimaUndangan::create([
                    'no_surat'  => $nomor_surat,
                    'penerima'  => $nip,
                    'ref'       => \App\Models\RsiaMemoInternal::class,
                ]);
            }

            // commit if success
            \Illuminate\Support\Facades\DB::commit();

            return isSuccess($memo_internal, 'Memo berhasil ditambahkan');
        } catch (\Exception $e) {
            // rollback if error
            \Illuminate\Support\Facades\DB::rollback();

            return isFail($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'dari'          => 'required',
            'perihal'       => 'required',
            'pj'            => 'required',
            'tanggal'       => 'required|date_format:Y-m-d',
            'content'       => 'required',
            'mengetahui'    => 'required',
            'penerima'      => 'required',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors()->all());
        }

        $data_surat_internal = [
            // 'no_surat'      => $request->no_surat,
            'perihal'       => $request->perihal,
            'tgl_terbit'    => $request->tanggal,

            'tempat'        => '-',
            'pj'            => $request->pj,
            'tanggal'       => '0000-00-00',
            'status'        => null,
        ];

        // mengetahui => nip petugas, bisa lebih dari satu dan dipisahkan dengan pipe (|)
        $data_memo_internal = [
            'dari'          => $request->dari,
            // 'no_surat'      => $request->no_surat,
            'content'       => $request->content,
            'mengetahui'    => $request->mengetahui,
        ];

        // penerima is array from post
        $penerima = $request->penerima ?? [];

        // penerima is json stringified, so we need to decode it first
        $penerima = json_decode($penerima);

        // surat_internal
        $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->first();
        if (!$surat_internal) {
            return isFail("Surat internal tidak ditemukan");
        }

        // memo_internal
        $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->first();
        if (!$memo_internal) {
            return isFail("Memo internal tidak ditemukan");
        }

        
        // db transaction for rollback if error for 2 table (rsia_surat_internal and rsia_memo_internal)
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // update to rsia_surat_internal
            $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->update($data_surat_internal);

            // update to rsia_memo_internal
            $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->update($data_memo_internal);
            
            // delete all penerima
            \App\Models\RsiaPenerimaUndangan::where('no_surat', $request->no_surat)->delete();
            
            // insert to rsia_penerima_undangan
            foreach ($penerima as $nip) {
                \App\Models\RsiaPenerimaUndangan::create([
                    'no_surat'  => $request->no_surat,
                    'penerima'  => $nip,
                    'ref'       => \App\Models\RsiaMemoInternal::class,
                ]);
            }

            // commit if success
            \Illuminate\Support\Facades\DB::commit();

            return isSuccess($memo_internal, 'Memo berhasil diupdate');
        } catch (\Exception $e) {
            // rollback if error
            \Illuminate\Support\Facades\DB::rollback();

            return isFail($e->getMessage());
        }
    }

    // delete
    public function delete(Request $request)
    {
        $rules = [
            'no_surat'      => 'required',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors()->all());
        }

        $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->first();

        // surat internal
        $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->first();
        if (!$surat_internal) {
            return isFail("Memo internal tidak ditemukan");
        }

        // memo internal
        if (!$memo_internal) {
            return isFail("Memo internal tidak ditemukan");
        }

        // update status on memo internal to 0 where no_surat = $request->no_surat
        $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->update(['status' => 0]);

        // update status on surat internal to 0
        $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->update(['status' => 0]);

        return isSuccess($memo_internal, 'Memo berhasil dihapus');
    }

    // destroy
    public function destroy(Request $request)
    {
        $rules = [
            'no_surat'      => 'required',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors()->all());
        }
        
        // make transaction for rollback if error
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // delete memo internal
            $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->delete();

            // delete surat internal
            $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->delete();

            // commit if success
            \Illuminate\Support\Facades\DB::commit();

            return isSuccess($memo_internal, 'Memo berhasil dihapus');
        } catch (\Exception $e) {
            // rollback if error
            \Illuminate\Support\Facades\DB::rollback();

            return isFail($e->getMessage());
        }
    }

    private function generateNomor(Request $request)
    {
        $data = \App\Models\RsiaSuratInternal::select('no_surat')
            ->orderBy('no_surat', 'desc')
            ->whereYear('tgl_terbit', date('Y'))
            ->first();

        if ($data) {
            $data = explode('/', $data->no_surat);
        } else {
            $data = [0];
        }

        if (!$request->tanggal) {
            return isFail("Tanggal terbit tidak boleh kosong");
        }

        // last number
        $date_now = $request->tanggal ? date('dmy', strtotime($request->tanggal)) : date('dmy');
        $last_number = $data[0];
        $last_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        $nomor_surat = $last_number . '/A/S-RSIA/' . $date_now;

        return $nomor_surat;
    }

    // render pdf
    public function renderPdf(Request $request, $nomor)
    {
        $nomor = str_replace('--', '/', $nomor);

        $memo = \App\Models\RsiaMemoInternal::with(['perihal', 'penerima', 'perihal.pegawai_detail', 'penerima.pegawai' => function ($query) {
            $query->select('nik', 'nama');
        }])->where('no_surat', $nomor)->first();
        
        if (!$memo) {
            return isFail("Memo tidak ditemukan");
        }

        $count_mengetahui = count(explode('|', $memo->mengetahui));
        $mengetahui = explode('|', $memo->mengetahui);

        // return view
        $html = view('print.memo_internal', [
            'memo' => $memo,
            'mengetahui' => $mengetahui,
            'count_mengetahui' => $count_mengetahui,
        ])->render();

        $pdf = PDF::loadHtml($html)->setWarnings(false)->setOptions([
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'isFontSubsettingEnabled' => true,
            'isJavascriptEnabled' => true,
        ]);

        // set papper size 210 mm x 330 mm

        // margin top, right, bottom, left
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);

        return $pdf->stream('memo_internal.pdf');
    }
}

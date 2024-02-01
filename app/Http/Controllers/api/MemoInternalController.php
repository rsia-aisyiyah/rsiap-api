<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemoInternalController extends Controller
{
    public function index(Request $request)
    {
        $memo = \App\Models\RsiaMemoInternal::with('perihal')->orderBy('no_surat', 'desc');

        if ($request->keyword) {
            $memo = $memo->where('no_surat', 'like', '%' . $request->keyword . '%')
                ->orWhere('mengetahui', 'like', '%' . $request->keyword . '%')
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

    public function store(Request $request)
    {
        $rules = [
            'no_surat'      => 'required',
            'perihal'       => 'required',
            'tanggal'       => 'required|date_format:Y-m-d',
            'content'       => 'required',
            'mengetahui'    => 'required',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors()->all());
        }

        $data_surat_internal = [
            'no_surat'      => $request->no_surat,
            'perihal'       => $request->perihal,
            'tanggal'       => $request->tanggal,

            'tempat'        => '-',
            'pj'            => '-',
            'status'        => null,
        ];

        // mengetahui => nip petugas, bisa lebih dari satu dan dipisahkan dengan pipe (|)
        $data_memo_internal = [
            'no_surat'      => $request->no_surat,
            'mengetahui'    => $request->mengetahui,
            'content'       => $request->content,
        ];

        // db transaction for rollback if error for 2 table (rsia_surat_internal and rsia_memo_internal)
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // insert to rsia_surat_internal
            $surat_internal = \App\Models\RsiaSuratInternal::create($data_surat_internal);

            // insert to rsia_memo_internal
            $memo_internal = \App\Models\RsiaMemoInternal::create($data_memo_internal);

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
            'no_surat'      => 'required',
            'perihal'       => 'required',
            'tanggal'       => 'required|date_format:Y-m-d',
            'content'       => 'required',
            'mengetahui'    => 'required',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors()->all());
        }

        $data_surat_internal = [
            'no_surat'      => $request->no_surat,
            'perihal'       => $request->perihal,
            'tanggal'       => $request->tanggal,

            'tempat'        => '-',
            'pj'            => '-',
            'status'        => null,
        ];

        // mengetahui => nip petugas, bisa lebih dari satu dan dipisahkan dengan pipe (|)
        $data_memo_internal = [
            'no_surat'      => $request->no_surat,
            'mengetahui'    => $request->mengetahui,
            'content'       => $request->content,
        ];

        // db transaction for rollback if error for 2 table (rsia_surat_internal and rsia_memo_internal)
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // update to rsia_surat_internal
            $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->update($data_surat_internal);

            // update to rsia_memo_internal
            $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->update($data_memo_internal);

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

        // update status on memo internal to 0
        $memo_internal = \App\Models\RsiaMemoInternal::where('no_surat', $request->no_surat)->update(['status' => 0]);

        // update status on surat internal to 0
        // $surat_internal = \App\Models\RsiaSuratInternal::where('no_surat', $request->no_surat)->update(['status' => 0]);

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
}

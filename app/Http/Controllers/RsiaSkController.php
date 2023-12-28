<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RsiaSkController extends Controller
{
    public function index(Request $request)
    {
        $rsia_sk = \App\Models\RsiaSk::with('penanggungjawab')->where('status', '1');

        if ($request->keyword) {
            $rsia_sk = $rsia_sk->where('status', '1')
                ->where('nomor', 'like', "%{$request->keyword}%")
                ->orWhere('judul', 'like', "%{$request->keyword}%")
                ->orWhere('pj', 'like', "%{$request->keyword}%")
                ->orWhereHas('penanggungjawab', function ($query) use ($request) {
                    $query->where('nama', 'like', "%{$request->keyword}%");
                });
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $rsia_sk->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $rsia_sk->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $rsia_sk->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, 'Data berhasil ditemukan');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'jenis' => 'required',
            'judul' => 'required',
            'pj' => 'required',
            'tgl_terbit' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // get last nomor in tgl_terbit year and where jenis ("A", "B", "C")
        $tglTerbit = date('Y', strtotime($request->tgl_terbit));
        $lastNomor = \App\Models\RsiaSk::whereYear('tgl_terbit', $tglTerbit)->where('jenis', $request->jenis)->max('nomor');

        if (!$lastNomor) {
            $lastNomor = 0;
        }

        // generate new nomor
        $newNomor = $lastNomor + 1;

        // create new sk
        $rsia_sk = \App\Models\RsiaSk::create([
            'nomor' => $newNomor,
            'jenis' => $request->jenis,
            'judul' => $request->judul,
            'pj' => $request->pj,
            'tgl_terbit' => $request->tgl_terbit,
        ]);

        return isSuccess($rsia_sk, 'Data berhasil ditambahkan');
    }

    public function show(Request $request)
    {
        if (!$request->nomor) {
            return isFail('SK tidak ditemukan', 404);
        }

        $rsia_sk = \App\Models\RsiaSk::with('penanggungjawab')->find($request->nomor);

        if (!$rsia_sk) {
            return isFail('SK tidak ditemukan', 404);
        }

        return isSuccess($rsia_sk, 'Data berhasil ditemukan');
    }

    public function update(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor' => 'required',
            'jenis' => 'required',
            'judul' => 'required',
            'pj' => 'required',
            'tgl_terbit' => 'required',

            'old_nomor' => 'required',
            'old_jenis' => 'required',
            'old_tgl_terbit' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }
        
        $rsia_sk = \App\Models\RsiaSk::where('nomor', $request->old_nomor)
            ->where('jenis', $request->old_jenis)
            ->where('tgl_terbit', $request->old_tgl_terbit)
            ->first();

        if (!$rsia_sk) {
            return isFail('SK tidak ditemukan', 404);
        }
        
        // mke db transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $rsia_sk->where('nomor', $request->old_nomor)
                ->where('jenis', $request->old_jenis)
                ->where('tgl_terbit', $request->old_tgl_terbit)
                ->update([
                    'nomor' => $request->nomor,
                    'jenis' => $request->jenis,
                    'judul' => $request->judul,
                    'pj' => $request->pj,
                    'tgl_terbit' => $request->tgl_terbit,
                ]);

            \Illuminate\Support\Facades\DB::commit();
            return isSuccess($rsia_sk, 'Data berhasil diubah');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($e->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor' => 'required',
            'jenis' => 'required',
            'tgl_terbit' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // find sk where nomor, jenis, tgl_terbit
        $rsia_sk = \App\Models\RsiaSk::where('nomor', $request->nomor)
            ->where('jenis', $request->jenis)
            ->where('tgl_terbit', $request->tgl_terbit)
            ->first();

        if (!$rsia_sk) {
            return isFail('SK tidak ditemukan', 404);
        }

        // update status to 0
        $rsia_sk->update([
            'status' => '0',
        ]);

        // return success
        return isSuccess($rsia_sk, 'Data berhasil dihapus');
    }

    public function destroy(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nomor' => 'required',
            'jenis' => 'required',
            'tgl_terbit' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // find sk where nomor, jenis, tgl_terbit
        $rsia_sk = \App\Models\RsiaSk::where('nomor', $request->nomor)
            ->where('jenis', $request->jenis)
            ->where('tgl_terbit', $request->tgl_terbit)
            ->first();

        if (!$rsia_sk) {
            return isFail('SK tidak ditemukan', 404);
        }

        // delete sk
        $rsia_sk->delete();

        // return success
        return isSuccess($rsia_sk, 'Data berhasil dihapus');
    }
}

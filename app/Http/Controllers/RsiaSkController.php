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
            // ->where('nomor', 'like', "%{$request->keyword}%")
                ->orWhere('judul', 'like', "%{$request->keyword}%")
                ->orWhere('pj', 'like', "%{$request->keyword}%")
                ->orWhereHas('penanggungjawab', function ($query) use ($request) {
                    $query->where('nama', 'like', "%{$request->keyword}%");
                });
        }

        // jenis
        if ($request->jenis) {
            $rsia_sk = $rsia_sk->where('jenis', $request->jenis);
        }

        if ($request->tgl_terbit) {
            $rsia_sk = $rsia_sk->where('tgl_terbit', $request->tgl_terbit);
        }

        // order by tgl_terbit desc and nomor desc
        $rsia_sk = $rsia_sk->orderBy('jenis', 'DESC')->orderBy('tgl_terbit', 'DESC')->orderBy('nomor', 'DESC');

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

    // public function store(Request $request)
    // {
    //     $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    //         'jenis' => 'required',
    //         'judul' => 'required',
    //         'pj' => 'required',
    //         'tgl_terbit' => 'required',
    //         'berkas' => 'mimes:pdf,doc,docx,jpg,jpeg,png|max:102400',
    //     ]);

    //     if ($validator->fails()) {
    //         return isFail($validator->errors(), 422);
    //     }

    //     // get last nomor in tgl_terbit year and where jenis ("A", "B", "C")
    //     $tglTerbit = date('Y', strtotime($request->tgl_terbit));
    //     $lastNomor = \App\Models\RsiaSk::whereYear('tgl_terbit', $tglTerbit)->where('jenis', $request->jenis)->max('nomor');

    //     if (!$lastNomor) {
    //         $lastNomor = 0;
    //     }

    //     // generate new nomor
    //     $newNomor = $lastNomor + 1;

    //     $file = $request->file('berkas');
    //     if ($file) {
    //         $file_name = strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName());
    //     }

    //     // create new sk
    //     $rsia_sk = \App\Models\RsiaSk::create([
    //         'nomor' => $newNomor,
    //         'jenis' => $request->jenis,
    //         'judul' => $request->judul,
    //         'pj' => $request->pj,
    //         'tgl_terbit' => $request->tgl_terbit,
    //         'berkas' => $file_name ?? "",
    //     ]);

    //     return isSuccess($rsia_sk, 'Data berhasil ditambahkan');
    // }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'jenis' => 'required',
            'judul' => 'required',
            'pj' => 'required',
            'tgl_terbit' => 'required',
            'berkas' => 'mimes:pdf,doc,docx,jpg,jpeg,png|max:102400',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // get file from request
        $file = $request->file('berkas');

        // get file name and randomize it
        $file_name = $file ? strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName()) : "";

        // Begin transaction
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
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
                'berkas' => $file_name,
            ]);

            // Commit the transaction as data has been successfully inserted
            \Illuminate\Support\Facades\DB::commit();

            // Upload the file if it exists
            if ($file) {
                $st = new \Illuminate\Support\Facades\Storage();

                if (!$st::disk('sftp')->exists(env('DOCUMENT_SK_SAVE_LOCATION'))) {
                    $st::disk('sftp')->makeDirectory(env('DOCUMENT_SK_SAVE_LOCATION'));
                }

                $st::disk('sftp')->put(env('DOCUMENT_SK_SAVE_LOCATION') . $file_name, file_get_contents($file));
            }

            return isSuccess($rsia_sk, 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            // Rollback the transaction in case of any exception
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }
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

            'berkas' => 'mimes:pdf,doc,docx,jpg,jpeg,png|max:102400',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // old data
        $rsia_sk = \App\Models\RsiaSk::where('nomor', $request->old_nomor)
            ->where('jenis', $request->old_jenis)
            ->where('tgl_terbit', $request->old_tgl_terbit)
            ->first();
        $old_file = $rsia_sk->berkas;

        if (!$rsia_sk) {
            return isFail('SK tidak ditemukan', 404);
        }

        // get file from request
        $file = $request->file('berkas');

        // get file name and randomize it
        $file_name = $file ? strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName()) : $rsia_sk->berkas;

        $data = [
            'nomor' => $request->nomor,
            'jenis' => $request->jenis,
            'judul' => $request->judul,
            'pj' => $request->pj,
            'tgl_terbit' => $request->tgl_terbit,
            'berkas' => $file_name,
        ];

        // mke db transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $rsia_sk->where('nomor', $request->old_nomor)
                ->where('jenis', $request->old_jenis)
                ->where('tgl_terbit', $request->old_tgl_terbit)
                ->update($data);

            \Illuminate\Support\Facades\DB::commit();

            // Upload the file if it exists
            if ($file) {
                $st = new \Illuminate\Support\Facades\Storage();

                if (!$st::disk('sftp')->exists(env('DOCUMENT_SK_SAVE_LOCATION'))) {
                    $st::disk('sftp')->makeDirectory(env('DOCUMENT_SK_SAVE_LOCATION'));
                }

                $st::disk('sftp')->put(env('DOCUMENT_SK_SAVE_LOCATION') . $file_name, file_get_contents($file));
            }

            // delete old file if exists
            if ($old_file && $old_file !== "" && $old_file !== $file_name) {
                $st = new \Illuminate\Support\Facades\Storage();

                if ($old_file && $st::disk('sftp')->exists(env('DOCUMENT_SK_SAVE_LOCATION') . $old_file)) {
                    $st::disk('sftp')->delete(env('DOCUMENT_SK_SAVE_LOCATION') . $old_file);
                }
            }

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
        
        // get file name
        $file = $rsia_sk->berkas;

        // delete sk
        $rsia_sk->delete();

        // delete file if exists
        if ($file && $file !== "") {
            $st = new \Illuminate\Support\Facades\Storage();

            if ($file && $st::disk('sftp')->exists(env('DOCUMENT_SK_SAVE_LOCATION') . $file)) {
                $st::disk('sftp')->delete(env('DOCUMENT_SK_SAVE_LOCATION') . $file);
            }
        }

        // return success
        return isSuccess($rsia_sk, 'Data berhasil dihapus');
    }
}

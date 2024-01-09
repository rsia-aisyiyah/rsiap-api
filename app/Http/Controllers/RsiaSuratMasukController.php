<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RsiaSuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $surat_masuk = \App\Models\RsiaSuratMasuk::select('*')->where('status', '1');

        if ($request->keyword) {
            $surat_masuk = $surat_masuk->where('status', '1')
                ->where('no_surat', 'like', '%' . $request->keyword . '%')
                ->orWhere('pengirim', 'like', '%' . $request->keyword . '%')
                ->orWhere('perihal', 'like', '%' . $request->keyword . '%')
                ->orWhere('tempat', 'like', '%' . $request->keyword . '%');
        }

        if ($request->via) {
            $surat_masuk = $surat_masuk->where('status', '1')->where('ket', 'like', '%' . $request->via . '%');
        }

        // no_simrs is date
        if ($request->no_simrs) {
            $surat_masuk = $surat_masuk->where('status', '1')->where('no_simrs', $request->no_simrs);
        }

        // tgl_surat is date
        if ($request->tgl_surat) {
            $surat_masuk = $surat_masuk->where('status', '1')->where('tgl_surat', $request->tgl_surat);
        }

        // data table or pagination
        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $surat_masuk->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $surat_masuk->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $surat_masuk->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, 'Data berhasil ditemukan');
    }

    public function detail($no)
    {
        $surat_masuk = \App\Models\RsiaSuratMasuk::where('no', $no)->first();

        if (!$surat_masuk) {
            return isFail([], 'Data tidak ditemukan', 404);
        }

        return isSuccess($surat_masuk, 'Data berhasil ditemukan');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_simrs' => 'required|date_format:Y-m-d',
            'pengirim' => 'required',
            'perihal' => 'required',
            'ket' => 'required',
            'berkas' => 'required|mimes:pdf,jpg,jpeg,png|max:28672',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // get file berkas 
        $file = $request->file('berkas');
        $file_name = strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName());
        
        $data = [
            'no_simrs' => $request->no_simrs,
            'no_surat' => $request->no_surat,
            'pengirim' => $request->pengirim,
            'tgl_surat' => $request->tgl_surat,
            'perihal' => $request->perihal,
            'pelaksanaan' => $request->pelaksanaan,
            'tempat' => $request->tempat,
            'ket' => $request->ket,
            'berkas' => $file_name,
            'status' => '1',
        ];

        // transaction if surat masuk success created
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $surat_masuk = \App\Models\RsiaSuratMasuk::create($data);
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }

        // move file to folder /webapps/rsia_surat_masuk using sftp
        $st = new \Illuminate\Support\Facades\Storage();
        if (!$st::disk('sftp')->exists(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION'))) {
            $st::disk('sftp')->makeDirectory(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION'));
        }

        // move file
        $st::disk('sftp')->put(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . $file_name, file_get_contents($file));

        return isSuccess($surat_masuk, 'Data berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no' => 'required|exists:rsia_surat_masuk,no',
            'no_simrs' => 'required',
            'pengirim' => 'required',
            'perihal' => 'required',
            'ket' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        $surat_masuk = \App\Models\RsiaSuratMasuk::where('no', $request->no)->first();

        if (!$surat_masuk) {
            return isFail([], 'Data tidak ditemukan', 404);
        }

        // get file berkas
        $file = $request->file('berkas');

        $data = [
            'no_simrs' => $request->no_simrs,
            'no_surat' => $request->no_surat,
            'pengirim' => $request->pengirim,
            'tgl_surat' => $request->tgl_surat,
            'perihal' => $request->perihal,
            'pelaksanaan' => $request->pelaksanaan,
            'tempat' => $request->tempat,
            'ket' => $request->ket,
            'status' => '1',
        ];

        // transaction if surat masuk success created
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $surat_masuk->update($data);
            \Illuminate\Support\Facades\DB::commit();

            if ($file) {
                // validate file mime type and max size 28mb
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'berkas' => 'mimes:pdf,jpg,jpeg,png|max:28672',
                ]);
    
                if ($validator->fails()) {
                    return isFail($validator->errors(), 422);
                }

                $fileName = strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName());
                $oldData = $surat_masuk->berkas;

                // update file from surat masuk
                $surat_masuk->update(['berkas' => $fileName]);

                // move file to folder /webapps/rsia_surat_masuk using sftp
                $st = new \Illuminate\Support\Facades\Storage();
                if (!$st::disk('sftp')->exists(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION'))) {
                    $st::disk('sftp')->makeDirectory(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION'));
                }

                // move file
                $st::disk('sftp')->put(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . $fileName, file_get_contents($file));

                // delete old file
                if ($st::disk('sftp')->exists(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . $oldData)) {
                    $st::disk('sftp')->delete(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . $oldData);
                }
            }
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }

        return isSuccess($surat_masuk, 'Data berhasil diubah');        
    }

    // delete = update status to 0
    public function delete($no)
    {
        $surat_masuk = \App\Models\RsiaSuratMasuk::where('no', $no)->first();

        if (!$surat_masuk) {
            return isFail([], 'Data tidak ditemukan', 404);
        }

        // transaction if surat masuk success created
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $surat_masuk->update(['status' => '0']);
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }

        return isSuccess($surat_masuk, 'Data berhasil dihapus');
    }

    // destroy = delete file and data
    public function destroy($no)
    {
        $surat_masuk = \App\Models\RsiaSuratMasuk::where('no', $no)->first();

        if (!$surat_masuk) {
            return isFail([], 'Data tidak ditemukan', 404);
        }

        $st = new \Illuminate\Support\Facades\Storage();

        if ($st::disk('sftp')->exists(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . $surat_masuk->berkas)) {
            $st::disk('sftp')->delete(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . $surat_masuk->berkas);
        }

        // transaction if surat masuk success created
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $surat_masuk->delete();
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }

        return isSuccess($surat_masuk, 'Data berhasil dihapus');
    }
}

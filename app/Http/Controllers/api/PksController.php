<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PksController extends Controller
{
    public function index(Request $request)
    {
        $pks = \App\Models\RsiaPks::with('pj_detail')->where('status', "1");

        if ($request->has('keyword') || $request->has('keywords')) {
            $keywords = $request->keyword ?? $request->keywords;
            $pks = $pks->where('no_pks_internal', 'like', '%' . $keywords . '%')
                ->orWhere('no_pks_eksternal', 'like', '%' . $keywords . '%')
                ->orWhere('judul', 'like', '%' . $keywords . '%')
                ->orWhereHas('pj_detail', function ($query) use ($keywords) {
                    $query->where('nama', 'like', '%' . $keywords . '%');
                });
        }

        // perpage
        if ($request->has('perpage')) {
            $pks = $pks->orderBy('id', 'DESC')->paginate(env('PER_PAGE', $request->perpage));
        } else {
            $pks = $pks->orderBy('id', 'DESC')->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($pks, 'Data PKS ditemukan');
    }

    public function getLastNomor(Request $request)
    {
        $internal = \App\Models\RsiaPks::select('no_pks_internal')->where('no_pks_internal', 'like', '%/A/%')->orderBy('id', 'DESC')->first();
        $eksternal = \App\Models\RsiaPks::select('no_pks_internal')->where('no_pks_internal', 'like', '%/B/%')->orderBy('id', 'DESC')->first();

        $pks = [
            'internal' => $internal ? $internal->no_pks_internal : null,
            'eksternal' => $eksternal ? $eksternal->no_pks_internal : null,
        ];

        return isSuccess($pks, 'Data PKS ditemukan');
    }

    // create
    public function store(Request $request)
    {
        // rules for data
        $rules = [
            'no_pks_internal' => 'required',
            'judul' => 'required',
            'pj' => 'required',
            'tanggal_awal' => 'required',
            // 'status' => 'required',
            'file' => 'required|mimes:pdf|max:2048',
        ];

        // validate
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        // validator fails
        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        $file = $request->file('file');
        $file_name = strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName());
        
        // move 
        $st = new \Illuminate\Support\Facades\Storage();
        // if directory not exists create it
        if (!$st::disk('sftp')->exists(env('DOCUMENT_PKS_SAVE_LOCATION'))) {
            $st::disk('sftp')->makeDirectory(env('DOCUMENT_PKS_SAVE_LOCATION'));
        }
        // move file
        $st::disk('sftp')->put(env('DOCUMENT_PKS_SAVE_LOCATION') . $file_name, file_get_contents($file));
        // final data
        $final_data = [
            'no_pks_internal' => $request->no_pks_internal,
            'no_pks_eksternal' => $request->no_pks_eksternal ?? "",
            'judul' => $request->judul,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir ?? "0000-00-00",
            'berkas' => $file_name,
            'pj' => $request->pj,
        ];

        $pks = \App\Models\RsiaPks::create($final_data);
        return isSuccess($pks, 'Data PKS berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pks = \App\Models\RsiaPks::find($id);
        
        if (!$pks) {
            return isFail('Data PKS tidak ditemukan', 404);
        }

        $rules = [
            'no_pks_internal' => 'required',
            'judul' => 'required',
            'pj' => 'required',
            'tanggal_awal' => 'required',
            'file' => 'mimes:pdf|max:2048',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // if file existss
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file_name = strtotime(now()) . '-' . str_replace([' ', '_'], '-', $file->getClientOriginalName());
            
            // move 
            $st = new \Illuminate\Support\Facades\Storage();
            // if directory not exists create it
            if (!$st::disk('sftp')->exists(env('DOCUMENT_PKS_SAVE_LOCATION'))) {
                $st::disk('sftp')->makeDirectory(env('DOCUMENT_PKS_SAVE_LOCATION'));
            }
            // move file
            $st::disk('sftp')->put(env('DOCUMENT_PKS_SAVE_LOCATION') . $file_name, file_get_contents($file));
            // final data
            $final_data = [
                'no_pks_internal' => $request->no_pks_internal,
                'no_pks_eksternal' => $request->no_pks_eksternal ?? "",
                'judul' => $request->judul,
                'tanggal_awal' => $request->tanggal_awal,
                'tanggal_akhir' => $request->tanggal_akhir ?? "0000-00-00",
                'berkas' => $file_name,
                'pj' => $request->pj,
            ];
        } else {
            $final_data = [
                'no_pks_internal' => $request->no_pks_internal,
                'no_pks_eksternal' => $request->no_pks_eksternal ?? "",
                'judul' => $request->judul,
                'tanggal_awal' => $request->tanggal_awal,
                'tanggal_akhir' => $request->tanggal_akhir ?? "0000-00-00",
                'pj' => $request->pj,
            ];
        }
        
        // old berkas
        $old_berkas = $pks->berkas;

        //  try update date using database transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // get old berkas and if new data has berkas and finished update delete old berkas
            $pks->update($final_data);
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }
        
        // delete old berkas
        if ($request->hasFile('file')) {
            $st = new \Illuminate\Support\Facades\Storage();
            if ($st::disk('sftp')->exists(env('DOCUMENT_PKS_SAVE_LOCATION') . $old_berkas)) {
                $st::disk('sftp')->delete(env('DOCUMENT_PKS_SAVE_LOCATION') . $old_berkas);
            }
        }

        return isSuccess($pks, 'Data PKS berhasil diupdate');
    }

    public function delete($id)
    {
        if (!$id) {
            return isFail('Data PKS tidak ditemukan', 404);
        }

        $pks = \App\Models\RsiaPks::find($id);

        if (!$pks) {
            return isFail('Data PKS tidak ditemukan', 404);
        }

        $pks->update(['status' => 0]);
        return isSuccess($pks, 'Data PKS berhasil dihapus');
    }

    public function destroy($id)
    {
        $pks = \App\Models\RsiaPks::find($id);

        if (!$pks) {
            return isFail('Data PKS tidak ditemukan', 404);
        }

        $st = new \Illuminate\Support\Facades\Storage();

        if ($st::disk('sftp')->exists(env('DOCUMENT_PKS_SAVE_LOCATION') . $pks->berkas)) {
            $st::disk('sftp')->delete(env('DOCUMENT_PKS_SAVE_LOCATION') . $pks->berkas);
        }

        $pks->delete();
        return isSuccess($pks, 'Data PKS berhasil dihapus');
    }
}

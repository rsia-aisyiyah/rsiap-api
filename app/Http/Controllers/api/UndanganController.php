<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UndanganController extends Controller
{
    public function index(Request $request)
    {
        $data = \App\Models\RsiaSuratInternalPenerima::select("*")
            ->with(['surat' => function ($q) {
                $q->with(['penanggung_jawab' => function ($q) {
                    $q->select('nik', 'nama');
                }]);
            }, 'notulen' => function ($q) {
                $q->select('no_surat', 'notulis_nik', 'created_at')->with(['notulis' => function ($q) {
                    $q->select('nik', 'nama');
                }]);
            }])
            ->where('status', '1')
            ->orderBy('no_surat', 'DESC')
            ->groupBy('no_surat');

        if ($request->keyword) {
            $data = $data->where('no_surat', 'like', '%' . $request->keyword . '%')
                ->orWhereHas('surat', function ($q) use ($request) {
                    $q->where('perihal', 'like', '%' . $request->keyword . '%')->orWhereHas('penanggung_jawab', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->keyword . '%');
                    });
                })
                ->orWhereHas('notulen', function ($q) use ($request) {
                    $q->whereHas('notulis', function ($q) use ($request) {
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

        return isSuccess($data, "Berhasil mendapatkan data");
    }

    public function me(Request $request)
    {
        $nip = $request->payload['sub'];
        $data = \App\Models\RsiaSuratInternalPenerima::select("*")
            ->with('surat')
            ->where('penerima', $nip)
            ->orderBy('no_surat', 'DESC')
            ->paginate(env('PER_PAGE', 10));

        return isSuccess($data, "Berhasil mendapatkan data");
    }
}

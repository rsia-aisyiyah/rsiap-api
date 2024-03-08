<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RsiaNotulenController extends Controller
{
    // get all notulen
    public function index(Request $request)
    {
        $notulen = \App\Models\RsiaNotulen::select('no_surat', 'notulis_nik', 'created_at')
            ->where('status', '1')
            ->orderBy('created_at', 'desc')
            ->with(['surat' => function ($query) {
                $query->with(['penanggung_jawab' => function ($query) {
                    $query->select('nik', 'nama');
                }])->select('no_surat', 'pj', 'perihal', 'tanggal', 'tgl_terbit', 'created_at');
            }, 'notulis' => function ($query) {
                $query->with(['jenjang_jabatan'])->select('nik', 'nama', 'departemen', 'jbtn', 'jnj_jabatan');
            }])
            ->withCount('peserta');

        if ($request->keyword) {
            $notulen = $notulen->where('no_surat', 'like', '%' . $request->keyword . '%')
                ->orWhereHas('surat', function ($query) use ($request) {
                    $query->where('perihal', 'like', '%' . $request->keyword . '%');
                })
                ->orWhereHas('notulis', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->keyword . '%');
                });
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $notulen->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $notulen->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $notulen->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, "Data notulen berhasil diambil");
    }

    // show notulen by no_surat
    public function show($nomor)
    {
        if (!$nomor) return isFail("Nomor surat tidak ditemukan");
        $nomor = str_replace('--', '/', $nomor);

        $notulen = \App\Models\RsiaNotulen::where('no_surat', $nomor)
            ->with(['surat' => function ($query) {
                $query->with(['penanggung_jawab' => function ($query) {
                    $query->select('nik', 'nama');
                }])->select('no_surat', 'pj', 'perihal', 'tanggal', 'tempat', 'created_at');
            }, 'notulis' => function ($query) {
                $query->with(['jenjang_jabatan'])->select('nik', 'nama', 'departemen', 'jbtn', 'jnj_jabatan');
            }])
            ->withCount('peserta')
            ->first();

        if (!$notulen) return isFail("Data notulen tidak ditemukan");

        return isSuccess($notulen, "Data notulen berhasil diambil");
    }

    // store
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|exists:rsia_surat_internal,no_surat',
            'pembahasan' => 'required',
        ]);
        
        if ($validator->fails()) return isFail($validator->errors());

        $notulen = \App\Models\RsiaNotulen::where('no_surat', $request->no_surat)->first();
        // if ($notulen) return isFail("Data notulen sudah ada");

        $data = [
            'no_surat' => $request->no_surat,
            'notulis_nik' => $request->payload['sub'],
            'pembahasan' => $request->pembahasan,
        ];

        if ($notulen) {
            $data['status'] = 1;

            $notulen->where('no_surat', $request->no_surat)->update($data);
        } else {
            $notulen = \App\Models\RsiaNotulen::create($data);
        }
        

        return isSuccess($notulen, "Data notulen berhasil ditambahkan");
    }

    // update
    public function update(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|exists:rsia_surat_internal,no_surat|exists:rsia_notulen,no_surat',
            'pembahasan' => 'required',
        ]);
        
        if ($validator->fails()) return isFail($validator->errors());

        $notulen = \App\Models\RsiaNotulen::where('no_surat', $request->no_surat)->first();
        if (!$notulen) return isFail("Data notulen tidak ditemukan");

        $data = [
            'no_surat' => $request->no_surat,
            'notulis_nik' => $request->payload['sub'],
            'pembahasan' => $request->pembahasan,
            'status' => 1,
        ];

        $notulen->where('no_surat', $request->no_surat)->update($data);

        return isSuccess($notulen, "Data notulen berhasil diubah");
    }

    // delete notulen (make status to 0)
    public function delete(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required'
        ]);
        
        if ($validator->fails()) return isFail($validator->errors());

        $notulen = \App\Models\RsiaNotulen::where('no_surat', $request->no_surat)->first();
        if (!$notulen) return isFail("Data notulen tidak ditemukan");

        $data = [
            'status' => 0
        ];

        // update where no_surat
        $notulen->where('no_surat', $request->no_surat)->update($data);

        $notulen->status = 0;

        return isSuccess($notulen, "Data notulen berhasil dihapus");
    }

    // render pdf
    public function renderPdf($nomor)
    {
        if (!$nomor) return isFail("Nomor surat tidak ditemukan");
        $nomor = str_replace('--', '/', $nomor);

        $notulen = \App\Models\RsiaNotulen::where('no_surat', $nomor)
            ->with(['surat' => function ($query) {
                $query->with(['penanggung_jawab' => function ($query) {
                    $query->select('nik', 'nama');
                }])->select('no_surat', 'pj', 'perihal', 'tempat', 'tanggal', 'created_at');
            }, 'notulis' => function ($query) {
                $query->with(['jenjang_jabatan'])->select('nik', 'nama', 'departemen', 'jbtn', 'jnj_jabatan');
            }])
            ->withCount('peserta')
            ->first();

        if (!$notulen) return isFail("Data notulen tidak ditemukan");

        $html = view('print.notulen', compact('notulen'))->render();

        $pdf = PDF::loadHtml($html)->setWarnings(false)->setOptions([
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'isFontSubsettingEnabled' => true,
            'isJavascriptEnabled' => true,
        ]);

        // margin top, right, bottom, left
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);

        return $pdf->stream('memo_internal.pdf');
    }

    // destroy notulen (delete from database) by no_surat
    public function destroy(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required'
        ]);
        
        if ($validator->fails()) return isFail($validator->errors());

        $notulen = \App\Models\RsiaNotulen::where('no_surat', $request->no_surat)->first();
        if (!$notulen) return isFail("Data notulen tidak ditemukan");

        $notulen->where('no_surat', $request->no_surat)->delete();

        return isSuccess($notulen, "Data notulen berhasil dihapus");
    }
}

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
                }])->where('status', '1');
            }])
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

    public function detail(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|string|exists:rsia_surat_internal,no_surat'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors()->first());
        }

        $data = \App\Models\RsiaSuratInternal::select("*")
            ->with(['penanggung_jawab' => function ($q) {
                $q->select('nik', 'nama');
            }, 'notulen', 'notulen.notulis' => function ($q) {
                $q->select('nik', 'nama'); 
            }, 'penerima', 'penerima.pegawai' => function($q) {
                $q->select('nik', 'nama');
            }])
            ->whereHas('penerima')
            ->where('no_surat', $request->no_surat)
            ->first();

        if (!$data) {
            return isFail("Data tidak ditemukan");
        }

        return isSuccess($data, "Berhasil mendapatkan data");
    }

    public function present(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|string|exists:rsia_surat_internal,no_surat',
            'nik' => 'string|exists:pegawai,nik'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors()->first());
        }   

        $nik = $request->nik ?? $request->payload['sub'];
        $karyawan =$request->karyawan ? $request->karyawan : [];

        if (count($karyawan) <= 0) {
            // cek apakah termasuk penerima
            $penerima = \App\Models\RsiaSuratInternalPenerima::where('no_surat', $request->no_surat)->where('penerima', $nik)->first();
            if (!$penerima) {
                return isFail("Anda tidak terdaftar sebagai penerima undangan, silahkan hubungi sekretariat untuk informasi lebih lanjut"); 
            }

            // cek sudah presensi atau belum
            $kehadiran = \App\Models\RsiaKehadiranRapat::where('no_surat', $request->no_surat)->where('nik', $nik)->first();
            if ($kehadiran) {
                return isOk("Anda sudah melakukan presensi rapat pada : " . \Carbon\Carbon::parse($kehadiran->created_at)->isoFormat('dddd, D MMMM Y') . " " . \Carbon\Carbon::parse($kehadiran->created_at)->format('H:i:s'));
            }

            // create
            $data = \App\Models\RsiaKehadiranRapat::create([
                'no_surat' => $request->no_surat,
                'nik' => $nik,
            ]);
    
            if (!$data) {
                return isFail("Gagal melakukan presensi rapat");
            }
    
            return isSuccess($data, "Berhasil melakukan presensi rapat");
        }

        // jika ada karyawan yang diinputkan 
        
        // hapus semua presensi berdasaarkan no_surat
        $delete = \App\Models\RsiaKehadiranRapat::where('no_surat', $request->no_surat)->delete();
        if (!$delete) {
            return isFail("Gagal melakukan presensi rapat");
        }

        // create
        foreach ($karyawan as $v) {
            // cek nik on penerima
            $penerima = \App\Models\RsiaSuratInternalPenerima::where('no_surat', $request->no_surat)->where('penerima', $v)->first();
            if (!$penerima) {
                $penerima = \App\Models\RsiaSuratInternalPenerima::create([
                    'no_surat' => $request->no_surat,
                    'penerima' => $v,
                ]);
            }

            $data = \App\Models\RsiaKehadiranRapat::create([
                'no_surat' => $request->no_surat,
                'nik' => $v,
            ]);
    
            if (!$data) {
                return isFail("Gagal melakukan presensi rapat");
            }
        }

        return isOk("Sejumlah " . count($karyawan) . " karyawan berhasil di presensi");
    }
}

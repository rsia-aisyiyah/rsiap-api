<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use function PHPSTORM_META\map;

/** @authenticated
 * @group Cuti Pegawai
 * 
 * data cuti pegawai, pada end points ini terdapat beberapa fitur seperti :
 * 1. Riwayat Cuti Pegawai
 * 2. pengajuan cuti pegawai
 * 3. Pengajuan cuti bersalin
 * */
class CutiController extends Controller
{
    /** @authenticated
     * Get Cuti Pegawai
     * 
     * pada end points in data yang diperoleh adalah data pegawai dan data cuti pegawai, data cuti pegawai meliputi data cuti bersalin (jika pegawai sedang cuti bersalin). cuti bersalin akan menghasilkan null jika pegawai tidak sedang cuti bersalin. 
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * */
    public function index(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::with('cuti.bersalin')
            ->where('nik', $request->nik)
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai);
    }

    public function counterCuti(Request $request)
    {
        $message = 'Gagal ambil data';

        if (!$request->nik) {
            return isFail('nik is required', 422);
        }
        // $cutiModel = new \App\Models\RsiaCuti();

        $hitung = DB::table('pegawai as t1')
            ->select(DB::raw("(SELECT count(id_pegawai) from rsia_cuti WHERE id_pegawai=t1.id and id_jenis = '1' and YEAR(tanggal_cuti)=year(curdate()) and MONTH(tanggal_cuti) < 07 and status_cuti='2' ) as jml1, (SELECT count(id_pegawai) from rsia_cuti WHERE id_pegawai=t1.id and id_jenis = '1' and MONTH(tanggal_cuti) > 06 and YEAR(tanggal_cuti)=year(curdate()) and MONTH(tanggal_cuti) <= 12 and status_cuti='2') as jml2"))
            ->where('t1.nik', $request->nik)
            ->get();

        // $hitung = $hitung->map(function($val){
        //     return [
        //         "jml1"  => (String)$val->jml1,
        //         "jml2"  => (String)$val->jml2
        //     ];
        // });

        if ($hitung) {
            return isSuccess($hitung);
        } else {
            return isOk($message);
        }
    }


    public function simpanCuti(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        } else if (!$request->id_pegawai) {
            return isFail('Id Pegawai is required', 422);
        } else if (!$request->nama) {
            return isFail('Nama is required', 422);
        } else if (!$request->dep_id) {
            return isFail('Departemen is required', 422);
        } else if (!$request->tanggal_cuti) {
            return isFail('Tanggal cuti is required', 422);
        } else if (!$request->id_jenis) {
            return isFail('Id jenis is required', 422);
        } else if (!$request->jenis) {
            return isFail('Jenis cuti is required', 422);
        }



        $message = 'Simpan cuti berhasil';
        $cutiModel = new \App\Models\RsiaCuti();
        $cutiBersalinModel = new \App\Models\RsiaCutiBersalin();

        $check = $cutiModel->where('nik', $request->nik)
            ->where('tanggal_cuti', $request->tanggal_cuti)
            ->first();

        if ($check) {
            return isOk('Data cuti sudah diajukan pada tanggal tersebut');
        }

        $start = \Illuminate\Support\Carbon::parse($request->tanggal_cuti['start'])->format('Y-m-d');
        $end = \Illuminate\Support\Carbon::parse($request->tanggal_cuti['end'])->format('Y-m-d');
        $data = [
            'id_pegawai'    => $request->id_pegawai,
            'nik'           => $request->nik,
            'nama'          => $request->nama,
            'dep_id'        => $request->dep_id,
            'tanggal_cuti'       => $start,
            'id_jenis'              => $request->id_jenis,
            'jenis'              => $request->jenis,
            'status_cuti'        => '0',
            'tanggal_pengajuan'  => date('Y-m-d H:i:s'),
        ];

        if (!$cutiModel->create($data)) {
            return isFail('Simpan data gagal');
        }

        if ($request->jenis == "Cuti Bersalin") {
            $id_cuti = \App\Models\RsiaCuti::orderBy('id_cuti', 'desc')->where('nik', $request->nik)
                ->first();

            $data = [
                'id_cuti' => $id_cuti->id_cuti,
                'tgl_mulai' => $start,
                'tgl_selesai' => $end,
            ];

            if (!$cutiBersalinModel->create($data)) {
                return isFail('Simpan data gagal');
            }
        }

        return isOk($message);
    }

    public function hapusCuti(Request $request)
    {
        $message = 'Hapus cuti berhasil';
        $cutiModel = new \App\Models\RsiaCuti();

        $data = [
            'id_cuti'    => $request->id_cuti,
        ];

        if (!$cutiModel->where($data)->delete()) {
            return isFail('Hapus data gagal');
        }

        return isOk($message);
    }
}

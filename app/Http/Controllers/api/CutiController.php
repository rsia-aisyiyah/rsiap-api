<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

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


    public function simpanCuti(Request $request){
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

        $check = $cutiModel->where('nik', $request->nik)
            ->where('tanggal_cuti', $request->tanggal_cuti)
            ->first();

            if ($check) {
                return isOk('Data cuti sudah diajukan pada tanggal tersebut');
            }
        
            $data = [
                'id_pegawai'    => $request->id_pegawai,
                'nik'           => $request->nik,
                'nama'          => $request->nama,
                'dep_id'        => $request->dep_id,
                'tanggal_cuti'       => $request->tanggal_cuti,
                'id_jenis'              => $request->id_jenis,
                'jenis'              => $request->jenis,
                'status_cuti'        => '0',
                'tanggal_pengajuan'  => date('Y-m-d H:i:s'),
            ];

            if (!$cutiModel->create($data)) {
                return isFail('Simpan data gagal');
            }
    
            return isOk($message);

    }

    public function hapusCuti(Request $request){
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
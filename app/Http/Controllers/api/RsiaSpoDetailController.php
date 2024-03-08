<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RsiaSpoDetailController extends Controller
{
    // {
    //     nomor: '015/C/SPO-RSIA/050123',
    //     pengertian:'<p>Koreksi Data Rekam Medis Elektronik adalah Pengkoreksian data rekam medis elektronik yang akan dilakukan setelah data disimpan pada ERM</p>',
    //     tujuan: 'Sebagai acuan penerapan langkah-langkah dalam koreksi data\n' + 'rekam medis elektronik',
    //     kebijakan: '<ol><li>SK Direktur Nomor: <strong>054/A/SK-RSIA/010722</strong> tentang Pelayanan SIMRS</li><li>Peraturan Direktur Nomor : <strong>003/A/SK-RSIA/040123</strong> tentang Pedoman Elektronik Rekam Medis</li></ol>',
    //     prosedur: '<ol><li>User yang mempunya hak akses ke elektronik rekam medis , melakukan log in sesuai dengan ID dan password masingmasing.&nbsp;</li><li>User memilih data yang akan diubah ( hanya bisa memilih data yang diisi sesuai user login ).&nbsp;</li><li>User melakukan perubahan data elektronik rekam medis&nbsp;</li><li>Info perubahan dan tanggal perubahan akan tampil dibawah data yang sudah diubah</li></ol>'
    // }

    public function index(Request $request)
    {
        $spo_detail = \App\Models\RsiaSpoDetail::select("*");
        
        if (!$request->nomor) {
            return isFail('SPO tidak ditemukan', 404);
        }

        $spo_detail = $spo_detail->where('nomor', $request->nomor)->first();
        
        if (!$spo_detail) {
            return isFail('SPO tidak ditemukan', 404);
        }

        return isSuccess($spo_detail, 'Data SPO berhasil ditampilkan');
    }

    public function store(Request $request)
    {
        $data = $request->except('payload');

        // check if nomor exists
        $rsia_spo = \App\Models\RsiaSpo::select("*")->where('nomor', $data['nomor'])->first();
        if (!$rsia_spo) {
            return isFail('SPO tidak ditemukan', 404);
        }

        // check if nomor on detail
        $rsia_spo_detail = \App\Models\RsiaSpoDetail::select("*")->where('nomor', $data['nomor'])->first();
        
        // htmlspecialchar all data
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value);
        }
        
        // if exists, update
        if ($rsia_spo_detail) {
            $rsia_spo_detail->update($data);
            return isSuccess($rsia_spo_detail, 'Data SPO berhasil diupdate');
        }

        // if not exists, create
        $rsia_spo_detail = \App\Models\RsiaSpoDetail::create($data);
        return isSuccess($rsia_spo_detail, 'Data SPO berhasil ditambahkan');
    }
}

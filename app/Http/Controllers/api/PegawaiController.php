<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

/**
 * @group Pegawai
 * 
 * Endpoints untuk pegawai, pada endpoint ini terdapat beberapa fitur seperti :
 * 1. List pegawai
 * 2. Detail pegawai
 * 3. Cuti pegawai
 * 
 * */
class PegawaiController extends Controller
{
    /** @authenticated
     * Get List Pegawai
     * 
     * Untuk mengambil semua data pegawai, end point ini membutuhkan otorisasi JWT Token. jadikan bearer token dengan value token yang didapat dari login. 
     * */
    public function index()
    {
        $pegawai = \App\Models\Pegawai::get();
        return isSuccess($pegawai);
    }

    /** @authenticated
     * Get Detail Pegawai
     * 
     * Detail pegawai berdasarkan NIK, data yang diperoleh berupa data pegawai dan data dokter (jika pegawai adalah dokter), data pegawai meliputi departemen (untuk saat ini). sedangkan data dokter meliputi spesialis (jika pegawai adalah dokter).
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * 
     * */
    public function detail(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::where('nik', $request->nik);
        if ($this->isDokter($request->nik)) {
            $pegawai->with('dokter.spesialis');
            $pegawai->with('rsia_email_pegawai');
        } else {
            $pegawai->with('petugas');
            $pegawai->with('dpt');
            $pegawai->with('stts_kerja');
            $pegawai->with('rsia_email_pegawai');
            $pegawai->with('berkas_pegawai');
            $pegawai->with('berkas_pegawai.master_berkas_pegawai');
        }

        $pegawai = $pegawai->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai);
    }

    public function isDokter($nik)
    {
        $data = \App\Models\Dokter::where('kd_dokter', $nik)->first();
        return $data ? true : false;
    }

    public function updateEmail(Request $request){
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        } else if (!$request->email) {
            return isFail('Email is required', 422);
        }

       

        $message = 'Simpan email berhasil';
        $emailModel = new \App\Models\EmailPegawai();

        $cek_email = $emailModel->where('nik', $request->nik)->first();

        // print_r($request->nik);
        
        if ($cek_email) {
            $emailModel->where('nik',$request->nik)
            ->update([
                'email' => $request->email,
            ]);
        } else {
            $emailModel->create([
                'nik' => $request->nik,
                'email' => $request->email,
            ]);
        }

        return isOk($message);

    }
}
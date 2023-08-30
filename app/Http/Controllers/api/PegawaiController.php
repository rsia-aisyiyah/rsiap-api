<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

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
        } else {
            $pegawai->with('dpt');
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
}
<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @authenticated
 * @group Diklat
 * 
 * Endpoints untuk diklat pegawai, pada endpoint ini terdapat beberapa fitur seperti :
 * 1. List diklat pegawai
 * 2. Filter diklat pegawai
 * */ 
class DiklatController extends Controller
{
    /** @authenticated
     * Get Diklat Pegawai
     * 
     * Untuk mengambil data diklat pegawai, end point ini membutuhkan otorisasi JWT Token. jadikan bearer token dengan value token yang didapat dari login.
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->with('diklat.kegiatan')->first();
        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai, 'data diklat pegawai');
    }

    /** @authenticated
     * Get Diklat Pegawai
     * 
     * Untuk mengambil data diklat pegawai, end point ini membutuhkan otorisasi JWT Token. jadikan bearer token dengan value token yang didapat dari login. 
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * @bodyParam kategori string optional Kategori kegiatan. No-example
     * @bodyParam nama_kegiatan string optional Nama kegiatan. No-example
     * @bodyParam tempat string optional Tempat kegiatan. No-example
     * @bodyParam penyelenggara string optional Penyelenggara kegiatan. No-example
     * @return \Illuminate\Http\Response
     * */
    public function filter(Request $request)
    {
        $message = "data diklat pegawai";
        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        // diklat data
        $diklat = \App\Models\RsiaDiklat::with('kegiatan')->where('id_peg', $pegawai->id);

        // filter diklat
        if ($request->kategori) {
            $diklat->whereHas('kegiatan', function ($query) use ($request) {
                $query->where('kategori', $request->kategori);
            });
        }

        if ($request->nama_kegiatan) {
            $diklat->whereHas('kegiatan', function ($query) use ($request) {
                $query->where('nama_kegiatan', 'like', '%' . $request->nama_kegiatan . '%');
            });
        }

        if ($request->tempat) {
            $diklat->whereHas('kegiatan', function ($query) use ($request) {
                $query->where('tempat', 'like', '%' . $request->tempat . '%');
            });
        }

        if ($request->penyelenggara) {
            $diklat->whereHas('kegiatan', function ($query) use ($request) {
                $query->where('penyelenggara', 'like', '%' . $request->penyelenggara . '%');
            });
        }

        // get diklat data
        $diklat = $diklat->get();

        // push diklat data to pegawai
        $pegawai->diklat = $diklat;

        return isSuccess($pegawai, $message);
    }
}
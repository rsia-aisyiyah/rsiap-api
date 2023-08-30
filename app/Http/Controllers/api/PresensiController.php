<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function tmp(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->first();
        if (!$pegawai) {
            return isFail('Pegawai tidak ditemukan, pastikan NIK benar', 404);
        }

        $presensi = \App\Models\TemporaryPresensi::where('id', $pegawai->id)->get();

        if (!$presensi) {
            return isFail('Belum ada data presensi', 404);
        }

        return isSuccess($presensi);
    }

    public function rekap(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->first();
        if (!$pegawai) {
            return isFail('Pegawai tidak ditemukan, pastikan NIK benar', 404);
        }

        $presensi = \App\Models\RekapPresensi::where('id', $pegawai->id)->get();

        if (!$presensi) {
            return isFail('Belum ada data presensi', 404);
        }

        return isSuccess($presensi);
    }
}
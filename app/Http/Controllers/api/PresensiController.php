<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $message = "Data presensi ";
        $presensi = \App\Models\RekapPresensi::where('id', $pegawai->id);

        $start = date('Y-m-01');
        $end   = date('Y-m-t');

        if ($request->tanggal) {
            $start = \Illuminate\Support\Carbon::parse($request->tanggal['start'])->format('Y-m-d');
            $end   = \Illuminate\Support\Carbon::parse($request->tanggal['end'])->format('Y-m-d');

            $message .= "pada tanggal " . $start . " sampai " . $end;
        } else {
            $message .= "Pada bulan " . date('F Y');
        }

        // jam_datang is date_time i have only date, make filter by date
        $presensi->whereBetween(DB::raw('DATE(jam_datang)'), [$start, $end]);
        
        $message .= " berhasil dimuat";
        $presensi = $presensi->get();

        if (!$presensi) {
            return isFail('Belum ada data presensi', 404);
        }

        return isSuccess($presensi);
    }
}
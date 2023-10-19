<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JadwalPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        $message = 'Jadwal pegawai bulan ini';
        $jadwal  = \App\Models\JadwalPegawai::where('id', $pegawai->id)
            ->whereMonth('bulan', date('m'))
            ->whereYear('tahun', date('Y'))
            ->get();

        return isSuccess($jadwal, $message);
    }

    public function filter(Request $request)
    {
        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        $message = 'Jadwal pegawai bulan ini';
        $jadwal  = \App\Models\JadwalPegawai::where('id', $pegawai->id);

        if ($request->tahun) {
            $message .= ' tahun ' . $request->tahun;
            $jadwal->whereYear('tahun', $request->tahun);
        } else {
            $message .= ' tahun ' . date('Y');
            $jadwal->whereYear('tahun', date('Y'));
        }

        if ($request->bulan) {
            $message .= ' bulan ' . $request->bulan;
            $jadwal->where('bulan', $request->bulan);
        } else {
            $message .= ' bulan ' . date('m');
            $jadwal->where('bulan', date('m'));
        }

        $message .= ' untuk ' . $pegawai->nama . ' berhasil dimuat';
        $jadwal  = $jadwal->get();

        return isSuccess($jadwal, $message);
    }

    public function now(Request $request)
    {
        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }
        
        $currentDay = date('d');
        if (substr($currentDay, 0, 1) == 0) {
            $currentDay = substr($currentDay, 1);
        }

        $message = 'Jadwal pegawai hari ini';
        $jadwal  = \App\Models\JadwalPegawai::select("h" . $currentDay . ' as shift')
            ->where('id', $pegawai->id)
            ->where('bulan', date('m'))
            ->where('tahun', date('Y'))
            ->first();

        $jadwal->jam_masuk = \App\Models\JamMasuk::where('shift', $jadwal->shift)->first();

        return isSuccess($jadwal, $message);
    }
}
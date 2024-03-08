<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Jadwal Operasi
 * */
class JadwalOperasiController extends Controller
{
    public function index()
    {
        $payload = auth()->payload();
        
        $message = 'Data jadwal operasi berhasil dimuat';
        $jadwal  = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
            ->where('kd_dokter', $payload->get('sub'))
            ->orderBy('no_rawat', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($jadwal, $message);
    }

    public function now()
    {
        $payload = auth()->payload();
        
        $message = 'Data jadwal operasi hari ini berhasil dimuat';
        $jadwal  = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
            ->where('kd_dokter', $payload->get('sub'))
            ->where('tanggal', '>=', date('Y-m-d'))
            ->orderBy('no_rawat', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($jadwal, $message);
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        $payload = auth()->payload();
        
        $message = 'Data jadwal operasi ';
        $jadwal  = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
            ->where('kd_dokter', $payload->get('sub'));
        if ($tahun !== null) {
            $message .= 'tahun ' . $tahun . ' ';
            $jadwal->whereYear('tanggal', $tahun);
        }

        if ($tahun !== null && $bulan !== null) {
            $message .= 'bulan ' . $bulan . ' ';
            $jadwal->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $message .= 'tanggal ' . $tanggal . ' ';
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $jadwal->where('tanggal', '>=', $fullDate);
        }

        $jadwal = $jadwal->orderBy('no_rawat', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        $message .= 'berhasil dimuat';

        return isSuccess($jadwal, $message);
    }
}
<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JadwalOperasiController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $jadwal = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
            ->where('kd_dokter', $this->payload->get('sub'))
            ->orderBy('no_rawat', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }

    public function now()
    {
        $jadwal = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
            ->where('kd_dokter', $this->payload->get('sub'))
            ->where('tanggal', date('Y-m-d'))
            ->orderBy('no_rawat', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $jadwal = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tanggal', $tahun)
                ->orderBy('no_rawat', 'DESC')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null) {
            $jadwal = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('no_rawat', 'DESC')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $jadwal = \App\Models\BookingOperasi::with('regPeriksa', 'regPeriksa.penjab', 'regPeriksa.pasien', 'paketOperasi', 'rsiaDiagnosaOperasi')
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tanggal', $fullDate)
                ->orderBy('no_rawat', 'DESC')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }
}

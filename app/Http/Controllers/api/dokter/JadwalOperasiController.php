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

    public function jadwalOperasi()
    {
        $kd_dokter = $this->payload->get('sub');
        $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $kd_dokter)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(10);

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }

    public function jadwalOperasiNow()
    {
        $kd_dokter = $this->payload->get('sub');
        $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $kd_dokter)
            ->where('tanggal', date('Y-m-d'))
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(10);

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }

    function jadwalOperasiByDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $this->payload->get('sub'))
                ->where('tanggal', $fullDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(10);
        }

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }
}

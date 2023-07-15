<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function pasien()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->orderBy('tgl_registrasi', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    public function pasienNow()
    {
        $kd_dokter = $this->payload->get('sub');

        $pasien = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->orderBy('jam_reg', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    function pasienByDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $pasien = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $pasien = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
}

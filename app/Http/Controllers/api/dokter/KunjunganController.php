<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $kd_dokter = $this->payload->get('sub');
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }

    public function now()
    {
        $kd_dokter = $this->payload->get('sub');
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null) {
            $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->paginate(env('PER_PAGE', 20));
        }

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }
}

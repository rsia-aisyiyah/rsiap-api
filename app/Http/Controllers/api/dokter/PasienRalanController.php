<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasienRalanController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $message = 'Seluruh Pasien Rawat Jalan berhasil dimuat';
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
            ->where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ralan')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }
    
    public function now()
    {
        $message = 'Pasien Rawat Jalan hari ini berhasil dimuat';
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
            ->where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ralan')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $message = "Pasien Rawat Jalan tahun $tahun berhasil dimuat";
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null) {
            $message = "Pasien Rawat Jalan bulan $bulan tahun $tahun berhasil dimuat";
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $message = "Pasien Rawat Jalan tanggal $tanggal bulan $bulan tahun $tahun berhasil dimuat";
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        return isSuccess($pasien, $message);
    }
}

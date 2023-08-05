<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasienRanapController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien    = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik', 'kamarInap.kamar.bangsal'])
            ->where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ranap')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    public function all()
    {
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik', 'kamarInap', 'kamarInap.kamar', 'kamarInap.kamar.bangsal'])
            ->where('status_lanjut', 'Ranap')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    public function now()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien    = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ranap')
            ->whereHas('kamarInap', function ($query) {
                $query->where('tgl_keluar', '0000-00-00');
                $query->where('stts_pulang', '-');
            })
            ->with(['pasien', 'penjab', 'poliklinik', 'kamarInap', 'kamarInap.kamar', 'kamarInap.kamar.bangsal'])
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC');

        $pasien = $pasien->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        $message = 'Data berhasil dimuat';
        if ($tahun !== null) {
            $message .= ' pada tahun ' . $tahun;
            $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik', 'kamarInap', 'kamarInap.kamar', 'kamarInap.kamar.bangsal'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->where('status_lanjut', 'Ranap')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null) {
            $message .= ' bulan ' . $bulan;
            $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik', 'kamarInap', 'kamarInap.kamar', 'kamarInap.kamar.bangsal'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->where('status_lanjut', 'Ranap')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $message .= ' tanggal ' . $tanggal;
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien   = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik', 'kamarInap', 'kamarInap.kamar', 'kamarInap.kamar.bangsal'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->where('status_lanjut', 'Ranap')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        return isSuccess($pasien, $message);
    }
}
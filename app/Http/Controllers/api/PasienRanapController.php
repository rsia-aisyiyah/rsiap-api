<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Pasien Rawat Inap
 * */
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
        $pasien    = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ranap')
            ->with([
                'pasien',
                'penjab',
                'poliklinik',
                'kamarInap' => function ($q) {
                    return $q->where('stts_pulang', '<>', 'Pindah Kamar');
                },
                'kamarInap.kamar.bangsal'
            ])->whereHas('kamarInap', function ($query) {
                $query->where('stts_pulang', '<>', 'Pindah Kamar');
            })
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    public function all()
    {
        $pasien = \App\Models\RegPeriksa::where('status_lanjut', 'Ranap')
            ->with([
                'pasien',
                'penjab',
                'poliklinik',
                'kamarInap' => function ($q) {
                    return $q->where('stts_pulang', '-');
                },
                'kamarInap.kamar.bangsal'
            ])
            ->whereHas('kamarInap', function ($query) {
                $query->where('tgl_keluar', '0000-00-00');
                $query->where('stts_pulang', '-');
            })
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Semua data pasien rawat inap berhasil dimuat');
    }

    public function now()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien    = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ranap')
            ->with([
                'pasien',
                'penjab',
                'poliklinik',
                'kamarInap' => function ($q) {
                    return $q->where('stts_pulang', '-');
                },
                'kamarInap.kamar.bangsal'
            ])
            ->whereHas('kamarInap', function ($query) {
                $query->where('tgl_keluar', '0000-00-00');
                $query->where('stts_pulang', '-');
            })
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC');

        $pasien = $pasien->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Data pasien rawat inap hari ini berhasil dimuat');
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        $message = 'Data berhasil dimuat';
        $pasien  = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
            ->where('status_lanjut', 'Ranap')
            ->with([
                'pasien','penjab','poliklinik','kamarInap' => function ($q) {
                    return $q->where('stts_pulang', '<>', 'Pindah Kamar');
                },'kamarInap.kamar.bangsal'
            ])
            ->whereHas('kamarInap', function ($query) {
                $query->where('stts_pulang', '<>', 'Pindah Kamar');
            });

        if ($tahun !== null) {
            $message .= ' pada tahun ' . $tahun;
            $pasien->whereYear('tgl_registrasi', $tahun);
        }

        if ($tahun !== null && $bulan !== null) {
            $message .= ' bulan ' . $bulan;
            $pasien->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $message .= ' tanggal ' . $tanggal;
            $pasien->where('tgl_registrasi', $tahun . '-' . $bulan . '-' . $tanggal);
        }

        $pasien = $pasien->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }
}
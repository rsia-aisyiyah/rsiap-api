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
                'kamarInap.kamar.bangsal',
                'ranapGabung.regPeriksa.pasien'
            ])->whereHas('kamarInap', function ($query) {
                $query->where('stts_pulang', '<>', 'Pindah Kamar');
            })
            ->orderBy('no_rawat', 'DESC')
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
                'kamarInap.kamar.bangsal',
                'ranapGabung.regPeriksa.pasien'
            ])
            ->whereHas('kamarInap', function ($query) {
                $query->where('tgl_keluar', '0000-00-00');
                $query->where('stts_pulang', '-');
            })
            ->orderBy('no_rawat', 'DESC')
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
                'kamarInap.kamar.bangsal',
                'ranapGabung.regPeriksa.pasien'
            ])
            ->whereHas('kamarInap', function ($query) {
                $query->where('tgl_keluar', '0000-00-00');
                $query->where('stts_pulang', '-');
            })->orderBy('no_rawat', 'DESC');

        $pasien = $pasien->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Data pasien rawat inap hari ini berhasil dimuat');
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        $message = 'Data berhasil dimuat';
        $pasien  = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
            ->where('status_lanjut', 'Ranap')
            ->with([
                'pasien', 'penjab', 'poliklinik', 'kamarInap' => function ($q) {
                    return $q->where('stts_pulang', '<>', 'Pindah Kamar');
                }, 'kamarInap.kamar.bangsal', 'ranapGabung.regPeriksa.pasien'
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

        $pasien = $pasien->orderBy('no_rawat', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }

    public function gabung(Request $request)
    {
        $ranap = \App\Models\KamarInap::with(['regPeriksa.pasien', 'regPeriksa.dokter', 'regPeriksa.dokter.spesialis', 'kamar', 'ranapGabung.regPeriksa.dokter', 'ranapGabung.regPeriksa.pasien', 'kamar.bangsal', 'regPeriksa.penjab', 'regPeriksa.kamarInap'])->orderBy('no_rawat', 'DESC');

        if ($request->stts_pulang == 'Masuk') {
            $ranap->whereBetween('tgl_masuk', [$request->tgl_pertama, $request->tgl_kedua]);
        } else if ($request->stts_pulang == 'Pulang') {
            $ranap->whereBetween('tgl_keluar', [$request->tgl_pertama, $request->tgl_kedua]);
        } else {
            $ranap->where('stts_pulang', '-');
        }

        if ($request->kd_dokter) {
            $ranap->whereHas('regPeriksa', function ($q) use ($request) {
                $q->where('kd_dokter', $request->kd_dokter);
            });
        }

        if ($request->spesialis) {
            $ranap->whereHas('regPeriksa.dokter', function ($q) use ($request) {
                $q->where('kd_sps', $request->spesialis);
            });
        }

        if ($request->kamar) {
            $ranap->whereHas('kamar.bangsal', function ($q) use ($request) {
                $q->where('nm_bangsal', 'like', '%' . $request->kamar . '%');
            });
        }

        return isSuccess($ranap->paginate(env('PER_PAGE', 20)), 'data pasien belum pulang + rawat gabung berhasil di peroleh');
    }
}

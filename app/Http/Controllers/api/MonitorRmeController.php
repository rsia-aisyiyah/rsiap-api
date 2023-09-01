<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MonitorRmeController extends Controller
{
    function ugd(Request $request)
    {
        $pasien = \App\Models\RegPeriksa::where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ralan')
            ->with([
                'pasien',
                'penjab',
                'bridgingSep'                       => function ($query) {
                    $query->select('no_rawat', 'no_sep');
                },
                'dataTriaseIgd'                     => function ($query) {
                    $query->select('no_rawat', 'tgl_kunjungan');
                },
                'rsiaGeneralConsent'                => function ($query) {
                    $query->select('no_rawat', 'no_rkm_medis', 'ttd');
                },
                'penilaianAwalKeperawatanIgd'       => function ($query) {
                    $query->select('no_rawat', 'tanggal');
                },
                'penilaianAwalKeperawatanKebidanan' => function ($query) {
                    $query->select('no_rawat', 'tanggal');
                },
                'penilaianMedisIgd'                 => function ($query) {
                    $query->select('no_rawat', 'tanggal');
                },
                'pemeriksaanRalan'                  => function ($query) {
                    $query->select('no_rawat', 'tgl_perawatan');
                },
                'resepObat'                         => function ($query) {
                    $query->select('no_rawat', 'no_resep');
                },
            ])
            ->whereHas('poliklinik', function ($query) {
                $query->where('kd_poli', 'IGDK');
            });

        if ($request->datatables == 'true') {
            $pasien = $pasien->get();
            return DataTables::of($pasien)->make(true);
        }

        $pasien = $pasien->paginate(env('PER_PAGE', 20));
        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    function ranap(Request $request)
    {
        $pasien = \App\Models\RegPeriksa::where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ranap')
            ->with([
                'pasien',
                'penjab',
                'rsiaGeneralConsent'                => function ($query) {
                    $query->select('no_rawat', 'no_rkm_medis', 'ttd');
                },
                'transferPasienAntarRuang' => function ($query) {
                    $query->select('no_rawat', 'asal_ruang', 'ruang_selanjutnya');
                },
            ]);

        if ($request->datatables == 'true') {
            $pasien = $pasien->get();
            return DataTables::of($pasien)->make(true);
        }

        $pasien = $pasien->paginate(env('PER_PAGE', 20));
        return isSuccess($pasien, 'Data berhasil dimuat');
    }
}
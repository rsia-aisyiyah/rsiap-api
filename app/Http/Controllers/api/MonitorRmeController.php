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
        $pasien = \App\Models\RegPeriksa::select('no_rawat', 'no_rkm_medis', 'kd_pj', 'kd_dokter', 'kd_poli', 'tgl_registrasi', 'status_lanjut')
            ->where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ranap')
            ->with([
                'penjab',
                'pasien',
                'rsiaGeneralConsent'        => function ($query) {
                    $query->select('no_rawat', 'no_rkm_medis', 'ttd');
                },
                'transferPasienAntarRuang'  => function ($query) {
                    $query->select('no_rawat', 'asal_ruang', 'ruang_selanjutnya');
                },
                'pemeriksaanRanap'          => function ($query) {
                    $query->select('no_rawat', 'tgl_perawatan');
                },
                'rsiaVerifPemeriksaanRanap' => function ($query) {
                    $query->select('no_rawat', 'tgl_verif');
                },
                'grafikHarian'              => function ($query) {
                    $query->select('no_rawat', 'tgl_perawatan', 'suhu_tubuh', 'nadi');
                },
                // rekonsiliasiObat
                'rekonsiliasiObat'          => function ($query) {
                    $query->select('no_rawat', 'no_rekonsiliasi');
                },
                'skriningGizi'              => function ($query) {
                    $query->select('no_rawat', 'keterangan');
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
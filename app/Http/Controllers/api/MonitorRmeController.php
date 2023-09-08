<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MonitorRmeController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }
    function ugd(Request $request)
    {
        $pasien = \App\Models\RegPeriksa::select('no_rawat', 'no_rkm_medis', 'kd_pj', 'kd_dokter', 'kd_poli', 'tgl_registrasi', 'status_lanjut')
            ->where('status_lanjut', 'Ralan')
            ->with([
                'penjab',
                'pasien'                            => function ($query) {
                    $query->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'pekerjaan', 'alamat', 'no_tlp');
                },
                'bridgingSep'                       => function ($query) {
                    $query->select('no_rawat');
                },
                'dataTriaseIgd'                     => function ($query) {
                    $query->select('no_rawat');
                },
                'rsiaGeneralConsent'                => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianAwalKeperawatanIgd'       => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianAwalKeperawatanKebidanan' => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianMedisIgd'                 => function ($query) {
                    $query->select('no_rawat');
                },
                'pemeriksaanRalan'                  => function ($query) {
                    $query->select('no_rawat');
                },
                'resepObat'                         => function ($query) {
                    $query->select('no_rawat');
                },
            ])
            ->whereHas('poliklinik', function ($query) {
                $query->where('kd_poli', 'IGDK');
            });

        $start = date('Y-m-01');
        $end   = date('Y-m-t');
        
        if ($request->tgl_registrasi) {
            if ($request->tgl_registrasi['start'] != null && $request->tgl_registrasi['end'] != null) {
                $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
                $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');
            }
        }

        $pasien->whereBetween('tgl_registrasi', [$start, $end]);
        
        if ($request->pembiayaan && $request->pembiayaan != null && $request->pembiayaan != 'all') {
            $pasien->whereHas('penjab', function ($query) use ($request) {
                $query->where('png_jawab', 'LIKE', '%' . $request->pembiayaan . '%');
            });
        }

        if ($request->datatables == 'true') {
            $pasien = $pasien->get();
            return DataTables::of($pasien)->make(true);
        }

        $pasien = $pasien->paginate(env('PER_PAGE', 20));
        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    function ranap(Request $request)
    {
        $message = 'Data berhasil dimuat';
        $pasien  = \App\Models\RegPeriksa::select('no_rawat', 'no_rkm_medis', 'kd_pj', 'kd_dokter', 'kd_poli', 'tgl_registrasi', 'status_lanjut')
            ->where('status_lanjut', 'Ranap')
            ->orderBy('no_rawat', 'DESC')
            ->with([
                'penjab',
                'pasien'                    => function ($query) {
                    $query->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'pekerjaan', 'alamat', 'no_tlp');
                },
                'rsiaGeneralConsent'        => function ($query) {
                    $query->select('no_rawat');
                },
                'transferPasienAntarRuang'  => function ($query) {
                    $query->select('no_rawat');
                },
                'pemeriksaanRanap'          => function ($query) {
                    $query->select('no_rawat');
                },
                'rsiaVerifPemeriksaanRanap' => function ($query) {
                    $query->select('no_rawat');
                },
                'grafikHarian'              => function ($query) {
                    $query->select('no_rawat');
                },
                'rekonsiliasiObat'          => function ($query) {
                    $query->select('no_rawat');
                },
                'skriningGizi'              => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianMedisRalanAnak'           => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianMedisRalanKandungan'      => function ($query) {
                    $query->select('no_rawat');
                },
            ]);

        if ($request->tgl) {
            if ($request->tgl['start'] != null && $request->tgl['end'] != null) {
                $start = \Illuminate\Support\Carbon::parse($request->tgl['start'])->format('Y-m-d');
                $end   = \Illuminate\Support\Carbon::parse($request->tgl['end'])->format('Y-m-d');
    
                if ($request->status && $request->status != null && $request->status != 'all') {
                    if ($request->status == 'pulang') {
                        $message .= ' berdasarkan tanggal pulang ' . $start . ' sampai ' . $end;
                        $pasien->whereHas('kamarInap', function ($query) use ($start, $end) {
                            $query->whereBetween('tgl_keluar', [$start, $end]);
                            $query->where('stts_pulang', '<>', 'Pindah Kamar');
                        });
                    } else {
                        $message .= ' berdasarkan tanggal registrasi ' . $start . ' sampai ' . $end;
                        $pasien->whereBetween('tgl_registrasi', [$start, $end])->whereHas('kamarInap', function ($query) {
                            $query->where('stts_pulang', '-');
                        });
                    }
                } else {
                    $message .= ' berdasarkan tanggal registrasi ' . $start . ' sampai ' . $end;
                    $pasien->whereBetween('tgl_registrasi', [$start, $end]);
                }
            } else {
                $pasien->whereHas('kamarInap', function ($query) {
                    $query->where('stts_pulang', '-');
                });
            }
        } else {
            $pasien->whereHas('kamarInap', function ($query) {
                $query->where('stts_pulang', '-');
            });
        }

        if ($request->pembiayaan && $request->pembiayaan != null && $request->pembiayaan != 'all') {
            $pasien->whereHas('penjab', function ($query) use ($request) {
                $query->where('png_jawab', 'LIKE', '%' . $request->pembiayaan . '%');
            });
        }

        if ($request->datatables == 'true') {
            $pasien = $pasien->get();
            return DataTables::of($pasien)->make(true);
        }

        $pasien = $pasien->paginate(env('PER_PAGE', 20));
        return isSuccess($pasien, 'Data berhasil dimuat');
    }
}
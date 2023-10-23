<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class MonitorRmeController extends Controller
{
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
                'penilaianMedisRanap'           => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianMedisRanapKandungan'      => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianAwalKeperawatanRanap'     => function ($query) {
                    $query->select('no_rawat');
                },
                'penilaianAwalKeperawatanRanapAnak' => function ($query) {
                    $query->select('no_rawat');
                },
                'PenilaianAwalKeperawatanRanapNeonatus' => function ($query) {
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

    public function ermSpesialistRanap(Request $request) 
    {
        $dokter = \App\Models\Dokter::select('kd_dokter', 'nm_dokter')
            ->where('status', '1')
            ->where('kd_dokter', '<>', '-')
            ->whereIn('kd_sps', ['S0001', 'S0003'])->get();

        $dokter_map = $dokter->map(function ($item) use ($request) {
            $pasien = \App\Models\RegPeriksa::select('no_rawat', 'status_lanjut')
                ->where('status_lanjut', 'Ranap')
                ->where('status_bayar', 'sudah bayar')
                ->where('kd_dokter', $item->kd_dokter);
            
            if ($request->tgl_registrasi) {
                $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
                $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');
                $pasien->whereBetween('tgl_registrasi', [$start, $end]);
            } else {
                $pasien->whereBetween('tgl_registrasi', [date('Y-m-01'), date('Y-m-t')]);
            }

            $penilaian_medis_ranap = \App\Models\PenilaianMedisRanap::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();

            $penilaian_medis_ranap_kandungan = \App\Models\PenilaianMedisRanapKandungan::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();

            $pemeriksaan_ranap = \App\Models\PemeriksaanRanap::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->whereIn('nip', [$item->kd_dokter])
                ->count();
            
            $verifikasi_pemeriksaan_ranap = \App\Models\RsiaVerifPemeriksaanRanap::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();
            
            $resume_pasien_ranap = \App\Models\ResumePasienRanap::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();
            
            $verifikasi_resume_pasien_ranap = \App\Models\RsiaVerifResumeRanap::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();

            $item->jumlah_reg_periksa = $pasien->count();
            $item->jPenilaianMedisRanap = $penilaian_medis_ranap;
            $item->jPenilaianMedisRanapKandungan = $penilaian_medis_ranap_kandungan;
            $item->jPemeriksaanRanap = $pemeriksaan_ranap;
            $item->jVerifikasiPemeriksaanRanap = $verifikasi_pemeriksaan_ranap;
            $item->jResumePasienRanap = $resume_pasien_ranap;
            $item->jVerifikasiResumePasienRanap = $verifikasi_resume_pasien_ranap;

            return $item;            
        });

        if ($request->datatables) {
            if ($request->datatables == 'true' || $request->datatables == 1) {
                return DataTables::of($dokter_map)->make(true);
            } else {
                $dokter_map = new Paginator($dokter_map, $dokter_map->count(), env('PER_PAGE', 20), $request->page, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
            }
        } else {
            $dokter_map = new Paginator($dokter_map, $dokter_map->count(), env('PER_PAGE', 20), $request->page, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return isSuccess($dokter_map, 'Data berhasil dimuat');
    }

    public function ermSpesialistRalan(Request $request)
    {
        $msg = 'Data berhasil dimuat';
        $dokter = \App\Models\Dokter::select('kd_dokter', 'nm_dokter')
            ->where('status', '1')
            ->where('kd_dokter', '<>', '-')
            ->whereIn('kd_sps', ['S0001', 'S0003'])->get();

        $dokter_map = $dokter->map(function ($item) use ($request) {
            $pasien = \App\Models\RegPeriksa::select('no_rawat', 'status_lanjut')
                ->where('status_lanjut', 'Ralan')
                ->where('status_bayar', 'sudah bayar')
                ->where('kd_dokter', $item->kd_dokter);
            
            if ($request->tgl_registrasi) {
                $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
                $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');
                $pasien->whereBetween('tgl_registrasi', [$start, $end]);
            } else {
                $pasien->whereBetween('tgl_registrasi', [date('Y-m-01'), date('Y-m-t')]);
            }

            // count pemeriksaan ralan where no rawat in reg periksa->no rawat
            $pemeriksaan_ralan = \App\Models\PemeriksaanRalan::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->where('nip', $item->kd_dokter)
                ->get()->groupBy('no_rawat')->count();

            // count penilaian medis ralan where no rawat in reg periksa->no rawat
            $penilaian_medis_ralan = \App\Models\PenilaianMedisRalan::whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();

            // count reset obat where no rawat in reg periksa->no rawat
            $resep_obat = \App\Models\ResepObat::where('tgl_peresepan', '<>', '0000-00-00')
                ->whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->count();

            $item->jumlah_reg_periksa = $pasien->count();
            $item->jPemeriksaanRalan  = $pemeriksaan_ralan;
            $item->jPenilaianMedisRalan = $penilaian_medis_ralan;
            $item->jResepObat = $resep_obat;

            return $item;
        });

        if ($request->datatables) {
            if ($request->datatables == 'true' || $request->datatables == 1) {
                return DataTables::of($dokter_map)->make(true);
            } else {
                $dokter_map = new Paginator($dokter_map, $dokter_map->count(), env('PER_PAGE', 20), $request->page, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
            }
        } else {
            $dokter_map = new Paginator($dokter_map, $dokter_map->count(), env('PER_PAGE', 20), $request->page, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return isSuccess($dokter_map, 'Data berhasil dimuat');
    }

    public function ermSpesialistRalanDebug(Request $request)
    {
        $msg = 'Data berhasil dimuat';
        $dokter = \App\Models\Dokter::select('kd_dokter', 'nm_dokter')
            ->where('status', '1')
            ->where('kd_dokter', '<>', '-')
            ->whereIn('kd_sps', ['S0001', 'S0003'])->get();

        $dokter_map = $dokter->map(function ($item) use ($request) {
            $pasien = \App\Models\RegPeriksa::select('no_rawat', 'status_lanjut')
                ->where('status_lanjut', 'Ralan')
                ->where('status_bayar', 'sudah bayar')
                ->where('kd_dokter', $item->kd_dokter);
            
            if ($request->tgl_registrasi) {
                $start = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
                $end   = \Illuminate\Support\Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');
                $pasien->whereBetween('tgl_registrasi', [$start, $end]);
            } else {
                $pasien->whereBetween('tgl_registrasi', [date('Y-m-01'), date('Y-m-t')]);
            }

            // count pemeriksaan ralan where no rawat in reg periksa->no rawat
            $pemeriksaan_ralan = \App\Models\PemeriksaanRalan::select('no_rawat')->whereIn('no_rawat', $pasien->pluck('no_rawat')->toArray())
                ->whereIn('nip', [$item->kd_dokter])
                ->toSql();

            $item->jumlah_reg_periksa = $pasien->get()->groupBy('no_rawat')->count();
            $item->jPemeriksaanRalan  = $pemeriksaan_ralan;

            return $item;
        });

        if ($request->datatables) {
            if ($request->datatables == 'true' || $request->datatables == 1) {
                return DataTables::of($dokter_map)->make(true);
            } else {
                $dokter_map = new Paginator($dokter_map, $dokter_map->count(), env('PER_PAGE', 20), $request->page, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
            }
        } else {
            $dokter_map = new Paginator($dokter_map, $dokter_map->count(), env('PER_PAGE', 20), $request->page, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return isSuccess($dokter_map, 'Data berhasil dimuat');
    }
}

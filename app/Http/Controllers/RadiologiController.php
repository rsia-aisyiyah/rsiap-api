<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RadiologiController extends Controller
{
    // Pasien Radiologi
    public function index(Request $request)
    {
        $msg = "Data Permintaan radiologi";
        $kd_dokter = $request->payload->get('sub');
        $data = \App\Models\PeriksaRadiologi::select("*")->with([
            'hasil',
            'permintaan',
            'jenis',
            'regPeriksa' => function ($q) {
                return $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'status_lanjut')->with([
                    'pasien' => function ($q) {
                        return $q->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'alamat');
                    }
                ])->with(["penjab" => function ($q) {
                    return $q->select('kd_pj', 'png_jawab');
                },]);
            }
        ])->where('kd_dokter', $kd_dokter);

        if ($request->tgl) {
            $msg .= " tanggal: " . $request->tgl['start'] . " - " . $request->tgl['end'];
            $data = $data->whereBetween('tgl_periksa', [$request->tgl['start'], $request->tgl['end']]);
        } else {
            $msg .= " bulan ini";
            $data = $data->whereBetween('tgl_periksa', [date('Y-m-01'), date('Y-m-t')]);
        }

        if ($request->penjab) {
            $msg .= " dengan penjab: $request->penjab";
            $data = $data->whereHas('regPeriksa', function ($q) use ($request) {
                $q->whereHas('penjab', function ($q) use ($request) {
                    $q->where('png_jawab', 'like', "%$request->penjab%");
                });
            });
        }

        if ($request->status) {
            $msg .= " dengan status $request->status";
            $data = $data->where('status', $request->status);
        }

        $msg .= " berhasil diambil";
        $data = $data->orderBy('tgl_periksa', "DESC")->orderBy('jam', "DESC")->paginate(env('PER_PAGE', 10));

        return isSuccess($data, $msg);
    }

    public function now(Request $request)
    {
        $msg = "Data Permintaan radiologi";
        $data = \App\Models\PermintaanRadiologi::select("*")->with([
            'hasil' => function ($q) {
                return $q->select('no_rawat', 'tgl_periksa', 'jam');
            },
            'regPeriksa' => function ($q) {
                return $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'status_lanjut')->with([
                    'pasien' => function ($q) {
                        return $q->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'alamat');
                    }
                ])->with(["penjab" => function ($q) {
                    return $q->select('kd_pj', 'png_jawab');
                }]);
            }, 'permintaanPemeriksaan.jenis' => function ($q) {
                return $q->select('kd_jenis_prw', 'nm_perawatan');
            }
        ]);


        if ($request->tgl) {
            $msg .= " tanggal: " . $request->tgl;
            $data = $data->whereDate('tgl_permintaan', date('Y-m-d', strtotime($request->tgl)));
        } else {
            $msg .= " bulan ini";
            $data = $data->whereDate('tgl_permintaan', date('Y-m-d'));
        }

        $msg .= " berhasil diambil";
        $data = $data->where("tgl_sampel", "<>", "0000-00-00")->orderBy('tgl_permintaan', "DESC")->paginate(env('PER_PAGE', 10));

        return isSuccess($data, "Berhasil");
    }

    // Permintaan Radiologi
    public function permintaan(Request $request)
    {
        $msg = "Data Permintaan radiologi";
        $data = \App\Models\PermintaanRadiologi::select("*")->with(['regPeriksa' => function ($q) {
            return $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'status_lanjut')->with([
                'pasien' => function ($q) {
                    return $q->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'alamat');
                }
            ])->with(["penjab" => function ($q) {
                return $q->select('kd_pj', 'png_jawab');
            }]);
        }, 'permintaanPemeriksaan.jenis' => function ($q) {
            return $q->select('kd_jenis_prw', 'nm_perawatan');
        }]);

        if ($request->tgl) {
            $msg .= " tanggal: " . $request->tgl['start'] . " - " . $request->tgl['end'];
            $data = $data->whereBetween('tgl_permintaan', [$request->tgl['start'], $request->tgl['end']]);
        } else {
            $msg .= " bulan ini";
            $data = $data->whereBetween('tgl_permintaan', [date('Y-m-01'), date('Y-m-t')]);
        }

        $msg .= " berhasil diambil";
        $data = $data->where('tgl_sampel', "0000-00-00")->orderBy('tgl_permintaan', "DESC")->paginate(env('PER_PAGE', 10));

        return isSuccess($data, $msg);
    }

    // Permintaan Radiologi Hari Ini
    public function permintaanNow(Request $request)
    {
        $data = \App\Models\PermintaanRadiologi::select("*")->with(['permintaanPemeriksaan.jenis' => function ($q) {
            return $q->select('kd_jenis_prw', 'nm_perawatan');
        }, 'regPeriksa' => function ($q) {
            return $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'status_lanjut')->with([
                'pasien' => function ($q) {
                    return $q->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'alamat');
                }
            ])->with(["penjab" => function ($q) {
                return $q->select('kd_pj', 'png_jawab');
            }]);
        }]);

        $data = $data->whereDate('tgl_permintaan', date('Y-m-d'))->where('tgl_sampel', "0000-00-00")->get();

        return isSuccess($data, "Data permintaan radiologi hari ini berhasil diambil");
    }

    public function hasil(Request $request)
    {
        $data = \App\Models\PermintaanRadiologi::select("*")->with(["regPeriksa" => function ($q){
            return $q->select('no_rawat', 'no_rkm_medis', 'kd_pj', 'status_lanjut')->with([
                'pasien' => function ($q) {
                    return $q->select('no_rkm_medis', 'nm_pasien', 'tgl_lahir', 'jk', 'alamat');
                }
            ])->with(["penjab" => function ($q) {
                return $q->select('kd_pj', 'png_jawab');
            }]);
        }]);

        if ($request->no_rawat) {
            $data = $data->where('no_rawat', $request->no_rawat);
        } else {
            return isFail("Missing parameter no_rawat");
        }

        if ($request->tanggal) {
            $data = $data->where('tgl_hasil', $request->tanggal);
        } else {
            return isFail("Missing parameter tanggal");
        }

        if ($request->jam) {
            $data = $data->where('jam_hasil', $request->jam);
        } else {
            return isFail("Missing parameter jam");
        }

        $data = $data->with(['hasil', 'gambar', 'periksa.jenis' => function ($q) {
            return $q->select('kd_jenis_prw', 'nm_perawatan');
        }, 'periksa.dokter' => function ($q) {
            return $q->select('kd_dokter', 'nm_dokter');
        }, 'periksa.petugas' => function ($q) {
            return $q->select('nip', 'nama');
        }])->get();

        return isSuccess($data, "Data hasil pemeriksaan radiologi no_rawat: $request->no_rawat tanggal: $request->tanggal jam: $request->jam berhasil diambil");
    }
}

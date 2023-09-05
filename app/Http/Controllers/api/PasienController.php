<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @group Pasien Universal
 * */
class PasienController extends Controller
{
    protected $payload;
    protected $tracker;

    public function __construct()
    {
        $this->payload = auth()->payload();
        $this->tracker = new \App\Http\Controllers\TrackerSqlController();
    }

    public function index()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien    = \App\Models\RegPeriksa::with('poliklinik', 'pasien', 'penjab', 'kamarInap.kamar.bangsal')
            ->where('kd_dokter', $kd_dokter)
            ->orderBy('tgl_registrasi', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Seluruh Pasien berhasil dimuat');
    }

    public function now()
    {
        $kd_dokter = $this->payload->get('sub');

        $pasien = \App\Models\RegPeriksa::with('poliklinik', 'pasien', 'penjab', 'kamarInap.kamar.bangsal')
            ->where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->orderBy('jam_reg', 'DESC');

        // $pasien = $pasien->whereHas('kamarInap', function ($query) {
        //     $query->where('stts_pulang', '-');
        //     $query->orWhere('tgl_keluar', '0000-00-00');
        // });

        $pasien = $pasien->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, 'Pasien hari ini berhasil dimuat');
    }

    public function metricNow()
    {
        $kd_dokter = $this->payload->get('sub');
        $pesialis  = \App\Models\Dokter::getSpesialis($kd_dokter);

        // Ranap
        if (str_contains(strtolower($pesialis->nm_sps), 'umum')) {
            $pasienRanap = \App\Models\RegPeriksa::where('status_lanjut', 'Ranap')
                ->with([
                    'kamarInap' => function ($q) {
                        return $q->where('stts_pulang', '-');
                    }
                ])
                ->whereHas('kamarInap', function ($query) {
                    $query->where('tgl_keluar', '0000-00-00');
                    $query->where('stts_pulang', '-');
                })
                ->count();
        } else {
            $pasienRanap = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
                ->where('status_lanjut', 'Ranap')
                ->with([
                    'kamarInap' => function ($q) {
                        return $q->where('stts_pulang', '-');
                    }
                ])
                ->whereHas('kamarInap', function ($query) {
                    $query->where('tgl_keluar', '0000-00-00');
                    $query->where('stts_pulang', '-');
                })
                ->count();
        }

        // Ralan
        $pasienRalan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ralan');

        if (str_contains(strtolower($pesialis->nm_sps), 'anak')) {
            $pasienRalan = $pasienRalan->whereHas('poliklinik', function ($query) {
                $query->whereNotIn('nm_poli', ['IGDK', 'UGD']);
            });
        }

        $pasienRalan = $pasienRalan->count();


        // Jadwal Operasi
        $jadwalOperasi = \App\Models\BookingOperasi::where('kd_dokter', $kd_dokter)
            ->where('tanggal', ">=", date('Y-m-d'))
            ->count();

        $data = [
            'pasien_ralan'   => $pasienRalan,
            'pasien_ranap'   => $pasienRanap,
            'jadwal_operasi' => $jadwalOperasi
        ];

        return isSuccess($data, 'Data metric berhasil dimuat');
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $message = "Pasien tahun $tahun berhasil dimuat";
            $pasien  = \App\Models\RegPeriksa::with('poliklinik', 'pasien', 'penjab', 'kamarInap.kamar.bangsal')
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null) {
            $message = "Pasien bulan $bulan tahun $tahun berhasil dimuat";
            $pasien  = \App\Models\RegPeriksa::with('poliklinik', 'pasien', 'penjab', 'kamarInap.kamar.bangsal')
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $message  = "Pasien tanggal $tanggal bulan $bulan tahun $tahun berhasil dimuat";
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien   = \App\Models\RegPeriksa::with('poliklinik', 'pasien', 'penjab', 'kamarInap.kamar.bangsal')
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        return isSuccess($pasien, $message);
    }

    /**
     * search
     * 
     * @bodyParam keywords string
     * @bodyParam statusLanjut string
     * @bodyParam penjab string (kd_pj)
     * @bodyParam no_rawat string search example : rawat 2023/01/01/000001
     * @bodyParam rm string search example : rm 009380
     * 
     * @return \Illuminate\Http\JsonResponse 
     **/
    public function search(Request $request)
    {
        $message = 'Data berhasil dimuat';
        $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik', 'kamarInap.kamar.bangsal'])
            ->where('kd_dokter', $this->payload->get('sub'))
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC');

        if ($request->tgl_registrasi) {
            $start = Carbon::parse($request->tgl_registrasi['start'])->format('Y-m-d');
            $end   = Carbon::parse($request->tgl_registrasi['end'])->format('Y-m-d');

            if ($request->dateby) {
                if ($request->dateby == 'pulang') {
                    $message .= ' berdasarkan tanggal pulang ' . $start . ' sampai ' . $end;
                    $pasien->whereHas('kamarInap', function ($query) use ($start, $end) {
                        $query->whereBetween('tgl_keluar', [$start, $end]);
                        $query->where('stts_pulang', '<>', 'Pindah Kamar');
                    });
                } else {
                    $message .= ' berdasarkan tanggal registrasi ' . $start . ' sampai ' . $end;
                    $pasien->whereBetween('tgl_registrasi', [$start, $end]);
                    $pasien->whereHas('kamarInap', function ($query) {
                        $query->where('stts_pulang', '-');
                    });
                }
            } else {
                $message .= ' berdasarkan tanggal registrasi ' . $start . ' sampai ' . $end;
                $pasien->whereBetween('tgl_registrasi', [$start, $end]);
            }
        }

        if ($request->keywords) {
            $message .= ' dengan kata kunci ' . $request->keywords;
            $pasien->whereHas('pasien', function ($query) use ($request) {
                $query->where('nm_pasien', 'LIKE', '%' . $request->keywords . '%')
                    ->orWhere('no_rkm_medis', 'LIKE', '%' . $request->keywords . '%');
            });
        }

        if ($request->status_lanjut) {
            $message .= ' dengan status lanjut ' . $request->status_lanjut;
            $pasien->where('status_lanjut', $request->status_lanjut);
            if ($request->status_lanjut == 'Ranap') {
                $pasien->with([
                    'kamarInap' => function ($q) {
                        return $q->where('stts_pulang', '<>', 'Pindah Kamar');
                    }
                ]);
            }
        }

        if ($request->penjab) {
            $message .= ' dengan penjab ' . $request->penjab;
            $pasien->whereHas('penjab', function ($query) use ($request) {
                $query->where('png_jawab', 'LIKE', '%' . $request->penjab . '%');
            });
        }

        $pasien = $pasien->paginate(env('PER_PAGE', 20));
        return isSuccess($pasien, $message);
    }

    private function shortByNamaPoli($realData)
    {
        $collection = $realData->toArray();
        $data       = $collection['data'];
        usort($data, function ($a, $b) {
            return $a['poliklinik']['nm_poli'] <=> $b['poliklinik']['nm_poli'];
        });

        $collection['data'] = $data;
        return $collection;
    }

    /**
     * pemeriksaan
     *
     * @bodyParam no_rawat string required
     * @return \Illuminate\Http\JsonResponse
     * 
     * @authenticated
     */
    function pemeriksaan()
    {
        // if not post return error
        if (!request()->isMethod('post')) {
            return isFail('Method not allowed');
        }

        // if no data return error
        if (!request()->has('no_rawat')) {
            return isFail('No Rawat tidak boleh kosong');
        }

        // get reg periksa data by no rawat
        $regPeriksa = \App\Models\RegPeriksa::where('no_rawat', request()->no_rawat)->first();

        if (!$regPeriksa) {
            return isFail('No Rawat tidak ditemukan');
        }

        if ($regPeriksa->status_lanjut == 'Ranap') {
            $message = 'Pemeriksaan Ranap berhasil dimuat';
            $data    = \App\Models\RegPeriksa::where('no_rawat', request()->no_rawat)
                ->where('status_lanjut', 'Ranap')
                ->with([
                    'poliklinik',
                    'pasien',
                    'penjab',
                    'kamarInap.kamar.bangsal',
                    'pemeriksaanRanap' => function ($q) {
                        $q->orderBy('tgl_perawatan', 'DESC');
                        $q->orderBy('jam_rawat', 'DESC');
                    }
                ])
                ->first();

            $verifikasi = \App\Models\RsiaVerifPemeriksaanRanap::where('no_rawat', request()->no_rawat)
                ->with([
                    'petugas' => function ($q) {
                        $q->select('nip', 'nama');
                    }
                ])
                ->orderBy('tgl_perawatan', 'DESC')
                ->orderBy('jam_rawat', 'DESC')
                ->get();

            $data->pemeriksaan = $data->pemeriksaanRanap;
            unset($data->pemeriksaanRanap);

            foreach ($data->pemeriksaan as $key => $value) {
                foreach ($verifikasi as $key2 => $value2) {
                    if ($value->no_rawat == $value2->no_rawat && $value->tgl_perawatan == $value2->tgl_perawatan && $value->jam_rawat == $value2->jam_rawat) {
                        $data->pemeriksaan[$key]->verifikasi = $value2;
                        break;
                    } else {
                        $data->pemeriksaan[$key]->verifikasi = null;
                    }
                }
            }

        } else {
            $message = 'Pemeriksaan Ralan berhasil dimuat';
            $data    = \App\Models\RegPeriksa::where('no_rawat', request()->no_rawat)
                ->where('status_lanjut', 'Ralan')
                ->with([
                    'poliklinik',
                    'pasien',
                    'penjab',
                    'kamarInap.kamar.bangsal',
                    'pemeriksaanRalan' => function ($q) {
                        $q->orderBy('tgl_perawatan', 'DESC');
                        $q->orderBy('jam_rawat', 'DESC');
                    }
                ])
                ->first();

            $data->pemeriksaan = $data->pemeriksaanRalan;
            unset($data->pemeriksaanRalan);
        }

        return isSuccess($data, $message);
    }

    function pemeriksaanChart(Request $request)
    {
        // if not post return error
        if (!$request->isMethod('post')) {
            return isFail('Method not allowed');
        }

        // if no data return error
        if (!$request->has('no_rawat')) {
            return isFail('No Rawat tidak boleh kosong');
        }

        if (!$request->has('stts_lanjut')) {
            return isFail('Status lanjut tidak boleh kosong');
        }

        if (ucfirst($request->stts_lanjut) == 'Ranap') {
            $message = 'Pemeriksaan Ranap untuk chaart berhasil dimuat';
            $data    = \App\Models\RsiaGrafikHarian::with([
                'petugas' => function ($q) {
                    $q->select('nip', 'nama');
                }
            ])->where('no_rawat', $request->no_rawat)->get();
        } else {
            $message = 'Pemeriksaan Ralan untuk chaart berhasil dimuat';
            $data    = \App\Models\PemeriksaanRalan::select('tgl_perawatan', 'jam_rawat', 'suhu_tubuh', 'nadi', 'spo2', 'respirasi')
                ->where('no_rawat', $request->no_rawat)
                ->get();
        }

        return isSuccess($data, $message);
    }

    public function verifikasiSoap(Request $request)
    {

        if (!$request->isMethod('post')) {
            return isFail('Method not allowed');
        }

        if (!$request->has('no_rawat')) {
            return isFail('No Rawat tidak boleh kosong');
        }

        if (!$request->has('tgl_perawatan')) {
            return isFail('Tanggal perawatan tidak boleh kosong');
        }

        if (!$request->has('jam_rawat')) {
            return isFail('Jam rawat tidak boleh kosong');
        }

        $message = 'Verifikasi SOAP berhasil';

        $verifModel = new \App\Models\RsiaVerifPemeriksaanRanap;

        // check if data already exist
        $check = $verifModel->where('no_rawat', $request->no_rawat)
            ->where('tgl_perawatan', $request->tgl_perawatan)
            ->where('jam_rawat', $request->jam_rawat)
            ->first();

        if ($check) {
            return isOk('Data sudah diverifikasi pada tanggal ' . $check->tgl_verif . ' jam ' . $check->jam_verif);
        }

        $data = [
            'no_rawat'      => $request->no_rawat,
            'tgl_perawatan' => $request->tgl_perawatan,
            'jam_rawat'     => $request->jam_rawat,
            'tgl_verif'     => date('Y-m-d'),
            'jam_verif'     => date('H:i:s'),
            'verifikator'   => $this->payload->get('sub'),
        ];

        if (!$verifModel->create($data)) {
            return isFail('Verifikasi SOAP gagal');
        }

        // insert to tracker
        try {
            $this->tracker->insertSql($verifModel, $data);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                '#'         => "tracker",
                'success'   => false,
                'message'   => 'Verifikasi SOAP gagal',
                'error'     => $e->getMessage()
            ], 500);
        }

        return isOk($message);
    }
}
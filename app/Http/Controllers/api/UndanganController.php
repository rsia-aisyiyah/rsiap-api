<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UndanganController extends Controller
{
    /**
     * Mendapatkan data undangan rapat.
     * 
     * Mendapatkan data undangan rapat yang terdaftar dalam sistem. endpoint ini juga bisa digunakan untuk mencari data undangan berdasarkan nomor surat internal, perihal, nama penanggung jawab, dan nama notulis. endpoint ini juga bisa digunakan untuk mendapatkan data undangan dalam bentuk datatables.
     * 
     * @queryParam keyword string optional Keyword pencarian. Example: Budi
     * @queryParam datatables boolean optional Datatables mode. Example: 1 (optional, jika diisi maka akan mengembalikan data dalam bentuk datatables, jika tidak diisi maka akan mengembalikan data dalam bentuk paginasi)
     * 
     * @authenticated
     * 
     * @response {
     *  "success": true,
     *  "message": "Berhasil mendapatkan data",
     *  "data": { ... }
     * }
     * */ 
    public function index(Request $request)
    {
        $data = \App\Models\RsiaPenerimaUndangan::select("*")
            ->with(['surat' => function ($q) {
                $q->with(['penanggung_jawab' => function ($q) {
                    $q->select('nik', 'nama');
                }]);
            }, 'notulen' => function ($q) {
                $q->select('no_surat', 'notulis_nik', 'created_at')->with(['notulis' => function ($q) {
                    $q->select('nik', 'nama');
                }])->where('status', '1');
            }])
            ->orderBy('no_surat', 'DESC')
            ->groupBy('no_surat');

        if ($request->keyword) {
            $data = $data->where('no_surat', 'like', '%' . $request->keyword . '%')
                ->orWhereHas('surat', function ($q) use ($request) {
                    $q->where('perihal', 'like', '%' . $request->keyword . '%')->orWhereHas('penanggung_jawab', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->keyword . '%');
                    });
                })
                ->orWhereHas('notulen', function ($q) use ($request) {
                    $q->whereHas('notulis', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->keyword . '%');
                    });
                });
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $data->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $data->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $data->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, "Berhasil mendapatkan data");
    }

    /**
     * Medapatkan data undangan rapat saya.
     * 
     * Endpoint ini memungkinkan karyawan untuk mendapatkan data undangan yang ditujukan kepada karyawan tersebut. 
     * 
     * @authenticated
     * 
     * @response {
     *  "success": true,
     *  "message": "Berhasil mendapatkan data",
     *  "data": { ... }
     * } 
     * */ 
    public function me(Request $request)
    {
        $nip = $request->payload['sub'];
        $data = \App\Models\RsiaPenerimaUndangan::select("*")
            ->with('surat')
            ->where('penerima', $nip)
            ->orderBy('no_surat', 'DESC')
            ->paginate(env('PER_PAGE', 10));

        return isSuccess($data, "Berhasil mendapatkan data");
    }

    /**
     * Mendapatkan detail undangan rapat.
     * 
     * Mendapatkan detail undangan rapat berdasarkan nomor surat internal. dalam endpoint ini juga akan mendapatkan data penanggung jawab rapat dan notulen rapat.
     * 
     * @bodyParam no_surat string required Nomor surat internal. Example: 028/A/S-RSIA/150224
     * 
     * @authenticated
     * 
     * @response {
     *   "success": true,
     *   "message": "Berhasil mendapatkan data",
     *   "data": {
     *       "no_surat": "028/A/S-RSIA/150224",
     *       "perihal": "Testing Notifikasi Dari Cron",
     *       "tempat": "AULA LT.3",
     *       "pj": "1.209.0918",
     *       "tgl_terbit": "2024-02-15",
     *       "tanggal": "2024-02-15 13:00:00",
     *       "catatan": "-",
     *       "status": "pengajuan",
     *       "created_at": "2024-02-17 10:19:07",
     *       "penerima_count": 8,
     *       "penanggung_jawab": {
     *           "nik": "1.209.0918",
     *           "nama": "dr. Caesar Al Ahmed Daminggo, M.K.M"
     *       },
     *       "notulen": null
     *   }
     * }
     * */ 
    public function detail(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|string|exists:rsia_surat_internal,no_surat'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors()->first());
        }

        $data = \App\Models\RsiaSuratInternal::select("*")
            ->with(['penanggung_jawab' => function ($q) {
                $q->select('nik', 'nama');
            }, 'notulen' => function ($q) {
                $q->select('no_surat', 'notulis_nik', 'created_at')->with(['notulis' => function ($q) {
                    $q->select('nik', 'nama');
                }])->where('status', '1');
            }])
            ->withCount('penerima')
            ->whereHas('penerima')
            ->where('no_surat', $request->no_surat)
            ->first();

        if (!$data) {
            return isFail("Data tidak ditemukan");
        }

        return isSuccess($data, "Berhasil mendapatkan data");
    }

    /**
     * Mendapatkan data penerima undangan.
     * 
     * Mendapatkan data penerima undangan dari nomor surat internal tertentu. endpoint ini juga bisa digunakan untuk mencari data penerima undangan berdasarkan nama karyawan. endpoint ini juga bisa digunakan untuk mendapatkan data penerima undangan dalam bentuk datatables.
     * 
     * Anda akan mendapatkan 2 data, yaitu :
     * - penerimaHadir : data penerima undangan yang sudah melakukan presensi rapat
     * - penerima : semua data penerima undangan yang terdaftar, diformat dalam bentuk paginasi laravel atau datatables sesuai dengan parameter datatables
     * 
     * @bodyParam no_surat string required Nomor surat internal. Example: 028/A/S-RSIA/150224
     * @bodyParam keyword string optional Keyword nama karyawan. Example: Budi
     * @bodyParam datatables boolean optional Datatables mode. Example: 1 (optional, jika diisi maka akan mengembalikan data dalam bentuk datatables, jika tidak diisi maka akan mengembalikan data dalam bentuk paginasi)
     * 
     * @authenticated
     * 
     * @response {
     *  "success": true,
     *  "message": "Berhasil mendapatkan data",
     *  "data": {
     *    "penerimaHadir": [ ... ],
     *    "penerima": [ ... ]
     *  }
     * }
     * */
    public function penerima(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|string|exists:rsia_surat_internal,no_surat'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors()->first());
        }

        $data = \App\Models\RsiaPenerimaUndangan::select("*")
            ->with(['pegawai' => function ($q) {
                $q->select('nik', 'nama');
            }, 'kehadiran'])
            ->where('no_surat', $request->no_surat);

        if ($request->keyword) {
            $data = $data->whereHas('pegawai', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->keyword . '%');
            })->orWhere('penerima', 'like', '%' . $request->keyword . '%');
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $data->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $data->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $data->paginate(env('PER_PAGE', 10));
        }

        $penerimaHadir = \App\Models\RsiaPenerimaUndangan::select("penerima")
            ->where('no_surat', $request->no_surat)
            ->whereHas('kehadiran')
            ->get();

        // to array penerima hadir
        $penerimaHadir = $penerimaHadir->map(function ($item) {
            return $item->penerima;
        });

        $d = [
            'penerimaHadir' => $penerimaHadir,
            "penerima" => $data,
        ];

        return isSuccess($d, "Berhasil mendapatkan data");
    }

    /**
     * Melakukan presensi rapat.
     * 
     * Melakukan presensi rapat dengan nomor surat internal tertentu. karyawan yang tidak terdaftar sebagai penerima undangan tidak bisa melakukan presensi rapat. jika karyawan tersebut perlu diikutsertakan dalam rapat, maka karyawan tersebut harus ditambahkan ke dalam penerima undangan terlebih dahulu, atau menggunakan endpoint tambah/presensi. jika karyawan sudah melakukan presensi rapat, maka karyawan tersebut tidak bisa melakukan presensi rapat lagi.
     * 
     * @bodyParam no_surat string required Nomor surat internal. Example: 028/A/S-RSIA/150224
     * @bodyParam nik string optional NIK karyawan. Example: 3.928.0623 (optional, jika tidak diisi maka akan menggunakan NIK dari token)
     * 
     * @authenticated
     * 
     * @response {
     *  "success": true,
     *  "message": "Berhasil melakukan presensi rapat"
     * }
     */
    public function present(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|string|exists:rsia_surat_internal,no_surat',
            'nik' => 'string|exists:pegawai,nik'
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors()->first());
        }

        $nik = $request->nik ?? $request->payload['sub'];

        // cek apakah termasuk penerima
        $penerima = \App\Models\RsiaPenerimaUndangan::where('no_surat', $request->no_surat)->where('penerima', $nik)->first();
        if (!$penerima) {
            return isFail("Anda tidak terdaftar sebagai penerima undangan, silahkan hubungi sekretariat untuk informasi lebih lanjut.");
        }

        // cek sudah presensi atau belum
        $kehadiran = \App\Models\RsiaKehadiranRapat::where('no_surat', $request->no_surat)->where('nik', $nik)->first();
        if ($kehadiran) {
            return isOk("Anda sudah melakukan presensi rapat pada : " . \Carbon\Carbon::parse($kehadiran->created_at)->isoFormat('dddd, D MMMM Y') . " " . \Carbon\Carbon::parse($kehadiran->created_at)->format('H:i:s'));
        }

        // create
        $data = \App\Models\RsiaKehadiranRapat::create([
            'no_surat' => $request->no_surat,
            'nik' => $nik,
        ]);

        if (!$data) {
            return isFail("Gagal melakukan presensi rapat");
        }

        return isOk("Berhasil melakukan presensi rapat");
    }

    /**
     * Menaambahkan karyawan ke dalam presensi rapat dan penerima undangan.
     * 
     * dalam keadaan tertentu mungkin ada karyawan yang tidak terdaftar sebagai penerima undangan namun karena suatu alasan perlu diikutsertakan dalam rapat. untuk itu kita perlu menambahkan karyawan tersebut ke dalam penerima undangan dan melakukan presensi rapat. menggunakan endpoint ini kita bisa menambahkan karyawan ke dalam penerima undangan dan melakukan presensi rapat sekaligus.
     *
     * @bodyParam no_surat string required Nomor surat internal. Example: 028/A/S-RSIA/150224
     * @bodyParam karyawan string required JSON stringified array of nik karyawan. Example: ["3.928.0623", "3.920.1021"]
     * 
     * @authenticated
     * 
     * @response {
     *  "success": true,
     *  "message": "Sejumlah 2 karyawan berhasil di presensi"
     * }
     */
    public function tambahPresensi(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|string|exists:rsia_surat_internal,no_surat',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors()->first());
        }

        $karyawan = $request->karyawan ? $request->karyawan : [];

        // $karyawan is json stringified array hot to make them countable
        if ($request->karyawan && is_string($karyawan)) {
            $karyawan = json_decode($karyawan);
        }

        if (count($karyawan) <= 0) {
            return isFail("Karyawan tidak boleh kosong");
        }

        // create
        \Illuminate\Support\Facades\DB::beginTransaction();

        $data = [];
        foreach ($karyawan as $v) {
            // cek nik on penerima
            $penerima = \App\Models\RsiaPenerimaUndangan::where('no_surat', $request->no_surat)->where('penerima', $v)->first();
            if (!$penerima) {
                $penerima = \App\Models\RsiaPenerimaUndangan::create([
                    'no_surat'  => $request->no_surat,
                    'penerima'  => $v,
                ]);
            }

            // cek apakah sudah presensi jika sudah lewati
            $kehadiran = \App\Models\RsiaKehadiranRapat::where('no_surat', $request->no_surat)->where('nik', $v)->first();
            if ($kehadiran) {
                continue;
            }

            $data[] = [
                'no_surat' => $request->no_surat,
                'nik' => $v,
            ];
        }

        if (count($data) <= 0) {
            \Illuminate\Support\Facades\DB::rollBack();
            return isFail("Tidak ada karyawan yang bisa di presensi");
        }

        $data = \App\Models\RsiaKehadiranRapat::insert($data);

        if (!$data) {
            \Illuminate\Support\Facades\DB::rollBack();
            return isFail("Gagal melakukan presensi rapat");
        }

        \Illuminate\Support\Facades\DB::commit();
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Pegawai
 * 
 * Endpoints untuk pegawai, pada endpoint ini terdapat beberapa fitur seperti :
 * 1. List pegawai
 * 2. Detail pegawai
 * 3. Cuti pegawai
 * 
 * */
class PegawaiController extends Controller
{
    /** @authenticated
     * Get List Pegawai
     * 
     * Untuk mengambil semua data pegawai, end point ini membutuhkan otorisasi JWT Token. jadikan bearer token dengan value token yang didapat dari login. 
     * */
    public function index(Request $request)
    {
        $pegawai = \App\Models\Pegawai::select('nik', 'nama', 'jk', 'jbtn', 'departemen')->with('dpt');

        if ($request->aktif) {
            $pegawai->where('stts_aktif', $request->aktif);
        } else {
            $pegawai->where('stts_aktif', 'AKTIF');
        }

        $pegawai = $pegawai->whereHas('petugas', function ($q) {
            return $q->where('status', '!=', '0')->where("kd_jbtn", "!=", "-");
        })->orderBy('nama', 'ASC');


        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                return \Yajra\DataTables\DataTables::of($pegawai)->make(true);
            } else {
                return $pegawai->paginate(env('PER_PAGE', 10));
            }
        } else {
            return $pegawai->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($pegawai, 'Berhasil mengambil data pegawai');
    }

    public function get($nik)
    {
        $pegawai = \App\Models\Pegawai::where('nik', $nik)
            ->with(['petugas', 'rsia_departemen_jm'])
            ->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai, 'Berhasil mengambil data pegawai');
    }

    public function store(Request $request)
    {
        $table_insert = [
            'pegawai' => \App\Models\Pegawai::class,
            'petugas' => \App\Models\Petugas::class,
            'rsia_departemen_jm' => \App\Models\RsiaDepartemenJm::class,
        ];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($table_insert as $key => $modelClass) {
                $data = $request->get($key);
                $model = new $modelClass;
                $model->fill($data);
                $model->save();
            }

            \Illuminate\Support\Facades\DB::commit();

            return isOk('Berhasil menambahkan data pegawai');
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        $table_insert = [
            'pegawai' => \App\Models\Pegawai::class,
            'petugas' => \App\Models\Petugas::class,
            'rsia_departemen_jm' => \App\Models\RsiaDepartemenJm::class,
        ];

        $n = $request->nik;
        $d = $request->pegawai['id'];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($table_insert as $key => $modelClass) {
                // if petugas not nik but nip
                $data = $request->get($key);
                $model = $key == 'petugas' ? $modelClass::where('nip', $n)->first() : ($key == 'rsia_departemen_jm' ? $modelClass::where('id_jm', $d)->first() : $modelClass::where('nik', $n)->first());
                
                if ($model) {
                    $model->fill($data);
                    $key == 'petugas' ? $model->where('nip', $n)->update($data) : ($key == 'rsia_departemen_jm' ? $model->where('id_jm', $d)->update($data) : $model->where('id', $d)->update($data));
                } else {
                    $model = new $modelClass;
                    $model->fill($data);
                    $model->save();
                }

            }

            \Illuminate\Support\Facades\DB::commit();

            return isOk('Berhasil mengubah data pegawai');
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }
    }

    public function profile_upload(Request $request)
    {
        return $request->all();
        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        $pegawai->foto = $request->foto;
        $pegawai->save();

        return isSuccess($pegawai, 'Berhasil mengubah foto profil');
    }

    public function destroy(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->first();
        $petugas = \App\Models\Petugas::where('nip', $request->nik)->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        $rsia_departemen_jm = \App\Models\RsiaDepartemenJm::where('id_jm', $pegawai->id)->first();


        if (!$petugas) {
            return isFail('Petugas not found', 404);
        }

        if (!$rsia_departemen_jm) {
            return isFail('Departemen not found', 404);
        }


        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $rsia_departemen_jm->where('id_jm', $pegawai->id)->delete();
            $petugas->where('nip', $request->nik)->delete();
            $pegawai->where('nik', $request->nik)->delete();

            \Illuminate\Support\Facades\DB::commit();

            return isOk('Berhasil menghapus data pegawai');
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollback();
            return isFail($th->getMessage(), 500);
        }
    }

    /** @authenticated
     * Get Detail Pegawai
     * 
     * Detail pegawai berdasarkan NIK, data yang diperoleh berupa data pegawai dan data dokter (jika pegawai adalah dokter), data pegawai meliputi departemen (untuk saat ini). sedangkan data dokter meliputi spesialis (jika pegawai adalah dokter).
     * 
     * @bodyParam nik string required NIK pegawai. No-example
     * 
     * */
    public function detail(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        }

        $pegawai = \App\Models\Pegawai::where('nik', $request->nik);
        if ($this->isDokter($request->nik)) {
            $pegawai->with('dokter.spesialis');
            $pegawai->with('rsia_email_pegawai');
        } else {
            $pegawai->with('dpt');
            $pegawai->with('petugas');
            $pegawai->with('stts_kerja');
            $pegawai->with('rsia_email_pegawai');
        }

        $pegawai = $pegawai->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai, 'Berhasil mengambil data pegawai');
    }

    public function isDokter($nik)
    {
        $data = \App\Models\Dokter::where('kd_dokter', $nik)->first();
        return $data ? true : false;
    }

    public function updateEmail(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        } else if (!$request->email) {
            return isFail('Email is required', 422);
        }



        $message = 'Simpan email berhasil';
        $emailModel = new \App\Models\EmailPegawai();

        $cek_email = $emailModel->where('nik', $request->nik)->first();

        // print_r($request->nik);

        if ($cek_email) {
            $emailModel->where('nik', $request->nik)
                ->update([
                    'email' => $request->email,
                ]);
        } else {
            $emailModel->create([
                'nik' => $request->nik,
                'email' => $request->email,
            ]);
        }

        return isOk($message);
    }

    public function updateProfil(Request $request)
    {
        if (!$request->nik) {
            return isFail('NIK is required', 422);
        } else if (!$request->email) {
            return isFail('Email is required', 422);
        } else if (!$request->alamat) {
            return isFail('Alamat is required', 422);
        } else if (!$request->no_telp) {
            return isFail('No. HP is required', 422);
        }

        $message = 'Simpan profil berhasil';
        $emailModel = new \App\Models\EmailPegawai();
        $pegawaiModel = new \App\Models\Pegawai();
        $petugasModel = new \App\Models\Petugas();

        $cek_email = $emailModel->where('nik', $request->nik)->first();

        // print_r($request->nik);

        if ($cek_email) {
            $emailModel->where('nik', $request->nik)
                ->update([
                    'email' => $request->email,
                ]);
        } else {
            $emailModel->create([
                'nik' => $request->nik,
                'email' => $request->email,
            ]);
        }

        $pegawaiModel->where('nik', $request->nik)->update([
            'alamat' => $request->alamat,
        ]);

        $petugasModel->where('nip', $request->nik)->update([
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
        ]);

        return isOk($message);
    }

    public function get_lsdt()
    {
        // get last id pegawai
        $last_id = \App\Models\Pegawai::select('id')->orderBy('id', 'DESC')->first();

        $not_granted_data = ['', '-', '0', 'null', 'NULL', null];

        if (in_array($last_id->id, $not_granted_data)) {
            return isFail('terjadi kesalahan pada server', 500);
        } else {
            $last_id = $last_id->id + 1;
        }

        return isSuccess([
            'data' => $last_id,
        ], 'Berhasil mengambil data');
    }
}

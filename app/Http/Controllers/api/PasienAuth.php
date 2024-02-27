<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Hamcrest\Type\IsDouble;
use Illuminate\Http\Request;

class PasienAuth extends Controller
{
    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nm_pasien'     => 'required',
            'tmp_lahir'     => 'required',
            'tgl_lahir'     => 'required',
            'jk'            => 'required|in:L,P',
            'agama'         => 'required',
            'stts_nikah'    => 'required|in:BELUM MENIKAH,MENIKAH,JANDA,DUDA',
            'pekerjaan'     => 'required',
            'alamat'        => 'required',
            'nm_ibu'        => 'required',
            'email'         => 'required|email|unique:pasien',
            'no_ktp'        => 'required|unique:pasien|numeric|digits:16',
            'no_tlp'        => 'required|numeric|digits_between:10,13',
            'kd_pj'         => 'required',
            'namakeluarga'  => 'required',
            'pnd'           => 'in:TS,TK,SD,SMP,SMA,SLTA/SEDERAJAT,D1,D2,D3,D4,S1,S2,S3,-',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        $data = [
            // "no_rkm_medis"      => "",
            "nm_pasien"         => strtoupper($request->nm_pasien),
            "no_ktp"            => $request->no_ktp,
            "jk"                => strtoupper($request->jk),
            "tmp_lahir"         => strtoupper($request->tmp_lahir),
            "tgl_lahir"         => $request->tgl_lahir,
            "nm_ibu"            => strtoupper($request->nm_ibu),
            "alamat"            => strtoupper($request->alamat),
            "gol_darah"         => "-",
            "pekerjaan"         => strtoupper($request->pekerjaan),
            "stts_nikah"        => strtoupper($request->stts_nikah),
            "agama"             => strtoupper($request->agama),
            "tgl_daftar"        => date('Y-m-d'),
            "no_tlp"            => $request->no_tlp,
            "umur"              => $this->countUmur($request->tgl_lahir),
            "pnd"               => "-",
            "keluarga"          => "AYAH",
            "namakeluarga"      => strtoupper($request->namakeluarga),
            "kd_pj"             => $request->kd_pj,
            "no_peserta"        => "-",
            "kd_kel"            => "2",
            "kd_kec"            => "2",
            "kd_kab"            => "2",
            "pekerjaanpj"       => "-",
            "alamatpj"          => strtoupper($request->alamat),
            "kelurahanpj"       => "-",
            "kecamatanpj"       => "-",
            "kabupatenpj"       => "-",
            "perusahaan_pasien" => "-",
            "suku_bangsa"       => "0",
            "bahasa_pasien"     => "0",
            "cacat_fisik"       => "0",
            "email"             => $request->email,
            "nip"               => "",
            "kd_prop"           => "0",
            "propinsipj"        => "-",
        ];

        // get last no_rkm_medis from pasien table and increment it then save it to $data
        $lastNoRkmMedis = \App\Models\Pasien::orderBy('no_rkm_medis', 'desc')->first();
        $data['no_rkm_medis'] = $lastNoRkmMedis->no_rkm_medis + 1;

        // save to pasien table
        $pasien = \App\Models\Pasien::create($data);

        if ($pasien) {
            return isOk('Berhasil mendaftar');
        } else {
            return isFail('Gagal mendaftar');
        }
    }

    public function login(Request $request)
    {
        // login with no_rkm_medis and password is tgl_lahir
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_rkm_medis'  => 'required',
            'password'      => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // first 2 digits is date, next 2 digits is month, and last 4 digits is year
        $tgl_lahir = substr($request->password, 0, 2) . '-' . substr($request->password, 2, 2) . '-' . substr($request->password, 4, 4);
        $tgl_lahir = date('Y-m-d', strtotime($tgl_lahir));

        // find pasien by no_rkm_medis and tgl_lahir
        $pasien = \App\Models\Pasien::where('no_rkm_medis', $request->no_rkm_medis)->where('tgl_lahir', $tgl_lahir)->first();

        if (!$pasien) {
            return isFail('No. Rekam Medis atau Password salah');
        }

        $payloadable = [
            "sub" => $pasien->no_rkm_medis,
            "nama" => $pasien->nm_pasien,
            "email" => $pasien->email,
            
            'isDokter' => false,    
            'kd_dep' => '0',
            'nm_dep' => '0',    
        ];

        // custom payloadable 
        $token = \Tymon\JWTAuth\Facades\JWTAuth::claims($payloadable)->fromUser($pasien);

        return $this->respondWithToken($token);
    }

    // logout
    public function logout()
    {
        \Tymon\JWTAuth\Facades\JWTAuth::invalidate();

        return isOk('Berhasil logout');
    }

    // validate token
    public function validateToken()
    {
        return isOk('Token valid');
    }

    // get user
    public function getUser()
    {
        $token = \Tymon\JWTAuth\Facades\JWTAuth::getToken();
        $user = \Tymon\JWTAuth\Facades\JWTAuth::getPayload($token)->toArray();

        return isSuccess($user);
    }

    // Count Umur
    public function countUmur($tgl_lahir)
    {
        $tgl_lahir = explode('-', $tgl_lahir);
        $tgl_lahir = $tgl_lahir[2] . '-' . $tgl_lahir[1] . '-' . $tgl_lahir[0];
        $tgl_lahir = new \DateTime($tgl_lahir);
        $today = new \DateTime('today');
        $y = $today->diff($tgl_lahir)->y;
        $m = $today->diff($tgl_lahir)->m;
        $d = $today->diff($tgl_lahir)->d;
        return $y . ' Th ' . $m . ' Bl ' . $d . ' Hr';
    }

    // response with token
    protected function respondWithToken($token)
    {
        $exp = \Tymon\JWTAuth\Facades\JWTAuth::factory()->getTTL() / 60 / 60;
        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => round($exp) . ' hour'
        ], 200);
    }
}

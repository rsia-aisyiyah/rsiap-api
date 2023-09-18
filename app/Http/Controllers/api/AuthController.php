<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Pegawai;
use App\Models\RsiaUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        $credentials = request(['username', 'password']);

        $user = User::select(DB::raw('AES_DECRYPT(id_user, "nur") as id_user'), DB::raw('AES_DECRYPT(id_user, "nur") as username'))
            ->where('id_user', DB::raw('AES_ENCRYPT("' . $credentials['username'] . '", "nur")'))
            ->where('password', DB::raw('AES_ENCRYPT("' . $credentials['password'] . '", "windi")'))
            ->first();

        if (!$user) {
            return isUnauthenticated('Unauthorized');
        }

        $payloadable = [
            "sub" => $user->username,
        ];
        
        // check credentials username is in table dokter or not
        $checkDokter = Dokter::where('kd_dokter', $credentials['username'])->first();

        if ($checkDokter) {
            $user->username = $checkDokter->kd_dokter;

            $pegawai = Pegawai::with('dokter.spesialis')
                ->where('pegawai.nik', $credentials['username'])
                ->first();

            $user->pegawai = $pegawai;

            if ($pegawai->dokter->spesialis) {
                $payloadable['isDokter'] = true;
                $payloadable['kd_sps']  = $pegawai->dokter->spesialis->kd_sps;
                $payloadable['nm_sps'] = $pegawai->dokter->spesialis->nm_sps;
            }
        } else {
            $user->username = $user->username;

            $pegawai = Pegawai::with('dpt')
                ->where('pegawai.nik', $credentials['username'])
                ->first();

            $user->pegawai = $pegawai;

            if ($pegawai->dpt) {
                $payloadable['isDokter'] = false;
                $payloadable['kd_dep']  = $pegawai->dpt->dep_id;
                $payloadable['nm_dep'] = $pegawai->dpt->nama;
            }
        }
        
        $token = auth()->claims($payloadable)->login($user);
        if (!$token) {
            return isUnauthenticated('Unauthorized');
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        // get payload from token
        $payload = auth()->payload()->toArray();
        $pegawai = Pegawai::where('pegawai.nik', $payload['sub'])
            ->first();

        return isSuccess($pegawai, 'Data berhasil dimuat');
    }

    public function logout()
    {
        auth()->logout();
        return isOk('Successfully logged out');
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function validateToken()
    {
        $token = auth()->getToken();
        if (!$token) {
            return isUnauthenticated('Unauthorized');
        }

        return isOk('Token valid');
    }


    // ================================================= Room Auth

    public function roomLogin(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = RsiaUsers::where('username', $request->username)
            ->where('password', md5($request->password))
            ->first();

        if (!$user) {
            return isUnauthenticated('Unauthorized');
        }

        $payloadable = [
            "sub" => $user->username,
            "nama" => $user->nama,
            "dep" => $user->dep_id,
            "status" => $user->status,
            "peg" => $user->id_pegawai,
        ];

        $token = auth()->claims($payloadable)->login($user);

        if (!$token) {
            return isUnauthenticated('Unauthorized');
        }

        return $this->respondWithToken($token);
    }

    public function roomMe()
    {
        // get payload from token
        $payload = auth()->payload()->toArray();
        $pegawai = RsiaUsers::where('username', $payload['sub'])
            ->first();

        return isSuccess($pegawai, 'Data berhasil dimuat');
    }

    public function roomLogout()
    {
        auth()->logout();
        return isOk('Successfully logged out');
    }

    public function roomRefresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function roomValidateToken()
    {
        $token = auth()->getToken();
        if (!$token) {
            return isUnauthenticated('Unauthorized');
        }

        return isOk('Token valid');
    }


    protected function respondWithToken($token)
    {
        $exp = auth()->factory()->getTTL() / 60;
        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => round($exp) . ' hour'
        ], 200);
    }
}
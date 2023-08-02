<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        $credentials = request(['username', 'password']);

        $user = User::select(DB::raw('AES_DECRYPT(id_user, "nur") as id_user'), DB::raw('AES_DECRYPT(id_user, "nur") as username'))
            ->with('spesialis')
            ->where('id_user', DB::raw('AES_ENCRYPT("' . $credentials['username'] . '", "nur")'))
            ->where('password', DB::raw('AES_ENCRYPT("' . $credentials['password'] . '", "windi")'))
            ->first();


        if (!$user) {
            return isUnauthenticated('Unauthorized');
        }

        $pegawai = Pegawai::with('dokter.spesialis')
            ->where('pegawai.nik', $credentials['username'])
            ->first();

        $payloadable = [
            "sub" => $user->username,
        ];

        if ($pegawai) {
            if ($pegawai->dokter) {
                if ($pegawai->dokter->spesialis) {
                    $payloadable['sps'] = $pegawai->dokter->spesialis->kd_sps;
                    $payloadable['spss'] = $pegawai->dokter->spesialis->nm_sps;
                }
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
        $pegawai = Pegawai::select('pegawai.nama', 'pegawai.jbtn')
            ->where('pegawai.nik', $payload['sub'])
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

    protected function respondWithToken($token)
    {
        $exp = auth()->factory()->getTTL() / 60 / 24;
        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => round($exp) . ' days'
        ], 200);
    }
}
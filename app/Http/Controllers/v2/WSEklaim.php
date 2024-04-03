<?php

namespace App\Http\Controllers\v2;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WSEklaim extends Controller
{
    public function index(Request $request)
    {
        if (!$request->hash) {
            return ApiResponse::fail("Hash is required", 400);
        }
        
        $data = $request->hash;
        
        // request to INA_CBG_WS_URL using post method and using library HTTP Client on laravel
        try {
            $response = Http::post(env('INA_CBG_WS_URL', 'http://192.168.100.45/E-Klaim/ws.php'), [
                $data
            ]);

            // get response body from http://
            $response = $response->body();
        } catch (\Exception $e) {
            return ApiResponse::fail("Server is offline", 500);
        }

        return ApiResponse::success($response, "Success");
    }

    /**
     * Encrypts the given data using the specified key.
     *
     * @param mixed $data The data to be encrypted.
     * @param string $key The encryption key.
     * @return string The encrypted data.
     */
    static function encrypt($data, $key)
    {
        // binary key
        $key = hex2bin($key);

        // check length of key, must be 256 bit or 32 bytes
        if (mb_strlen($key, '8bit') !== 32) {
            throw new \Exception('Key must be 256 bit or 32 bytes');
        }

        // initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // encrypt data
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // create signature
        $signature = mb_substr(hash_hmac('sha256', $encrypted, $key, true), 0, 10, '8bit');

        // combine all data
        $encoded = chunk_split(base64_encode($signature . $iv . $encrypted));

        return $encoded;
    }

    /**
     * Decrypts the given data using the specified key.
     *
     * @param string $data The data to be decrypted.
     * @param string $key The key used for decryption.
     * @return string The decrypted data.
     */
    static function decrypt($data, $key)
    {
        // binary key
        $key = hex2bin($key);

        // check length of key, must be 256 bit or 32 bytes
        if (mb_strlen($key, '8bit') !== 32) {
            throw new \Exception('Key must be 256 bit or 32 bytes');
        }

        // calculate iv length
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');

        // breakdown parts
        $decoded = base64_decode($data);
        $signature = mb_substr($decoded, 0, 10, '8bit');
        $iv = mb_substr($decoded, 10, $iv_length, "8bit");
        $encrypted = mb_substr($decoded, $iv_length + 10, NULL, "8bit");

        // check signature
        $calc_signature = mb_substr(hash_hmac('sha256', $encrypted, $key, true), 0, 10, '8bit');

        if (!self::compareSignature($signature, $calc_signature)) {
            throw new \Exception('Signature mismatch');
        }

        // decrypt data
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return $decrypted;
    }

    /**
     * Compare two signatures.
     *
     * @param mixed $a The first signature to compare.
     * @param mixed $b The second signature to compare.
     * @return int Returns 0 if the signatures are equal, a positive number if $a is greater than $b, and a negative number if $a is less than $b.
     */
    static function compareSignature($a, $b)
    {
        /// compare individually to prevent timing attacks
        /// compare length
        if (strlen($a) !== strlen($b)) return false;

        /// compare individual
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $result == 0;
    }
}

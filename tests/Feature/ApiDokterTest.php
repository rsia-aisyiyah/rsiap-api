<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\ApiAuthTest;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiDokterTest extends TestCase
{
    protected $dokterUrl = 'http://localhost/rsiapi/api/dokter';

    function test_get_dokter()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl);

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_spesialis
    function test_get_spesialis()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/spesialis');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien
    function test_get_pasien()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_now
    function test_get_pasien_now()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/now');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_tahun
    function test_get_pasien_tahun()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/2023');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_tahun_bulan
    function test_get_pasien_tahun_bulan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/2023/06');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_tahun_bulan_tanggal
    function test_get_pasien_tahun_bulan_tanggal()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/2023/06/12');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // ---------- RANAP

    // test_get_pasien_ranap
    function test_get_pasien_ranap()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ranap');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ranap_now
    function test_get_pasien_ranap_now()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ranap/now');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ranap_tahun
    function test_get_pasien_ranap_tahun()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ranap/2023');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ranap_tahun_bulan
    function test_get_pasien_ranap_tahun_bulan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ranap/2023/06');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ranap_tahun_bulan_tanggal
    function test_get_pasien_ranap_tahun_bulan_tanggal()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ranap/2023/06/12');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // ---------- RALAN

    // test_get_pasien_ralan
    function test_get_pasien_ralan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ralan');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ralan_now
    function test_get_pasien_ralan_now()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ralan/now');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ralan_tahun
    function test_get_pasien_ralan_tahun()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ralan/2023');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ralan_tahun_bulan
    function test_get_pasien_ralan_tahun_bulan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ralan/2023/06');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_ralan_tahun_bulan_tanggal
    function test_get_pasien_ralan_tahun_bulan_tanggal()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/pasien/ralan/2023/06/12');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // ---------- JADWAL OPERASI

    // test_get_pasien_ralan
    function test_get_pasien_operasi()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/jadwal/operasi');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_operasi_now
    function test_get_pasien_operasi_now()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/jadwal/operasi/now');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_operasi_tahun
    function test_get_pasien_operasi_tahun()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/jadwal/operasi/2023');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_operasi_tahun_bulan
    function test_get_pasien_operasi_tahun_bulan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/jadwal/operasi/2023/06');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_pasien_operasi_tahun_bulan_tanggal
    function test_get_pasien_operasi_tahun_bulan_tanggal()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/jadwal/operasi/2023/06/12');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // ---------- JUNJUNGAN

    // test_get_kunjungan
    function test_get_kunjungan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/kunjungan');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_kunjungan_now
    function test_get_kunjungan_now()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/kunjungan/now');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_kunjungan_tahun
    function test_get_kunjungan_tahun()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/kunjungan/2023');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_kunjungan_tahun_bulan
    function test_get_kunjungan_tahun_bulan()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/kunjungan/2023/06');
        
        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    // test_get_kunjungan_tahun_bulan_tanggal
    function test_get_kunjungan_tahun_bulan_tanggal()
    {
        $token = new ApiAuthTest();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($this->dokterUrl . '/kunjungan/2023/06/12');

        if ($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }
}

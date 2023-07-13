<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    protected $authUrl = 'http://localhost/rsiapi/api/auth';

    protected $token = '';

    // getToken = 
    public function getToken() {
        $response = Http::post($this->authUrl . '/login', [
            'username' => '1.101.1112',
            'password' => 'dokter123'
        ]);

        if($response->successful()) {
            return $response->json()['access_token'];
        } else {
            return '';
        }
    }

    public function test_login()
    {
        if($this->getToken() != '') {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function test_me()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->authUrl . '/me');

        if($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function test_validate()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->authUrl . '/validate');

        if($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function test_refresh()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->authUrl . '/refresh');

        if($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function test_logout()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->authUrl . '/logout');

        if($response->json()['success'] == true) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }
}

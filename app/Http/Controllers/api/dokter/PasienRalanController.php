<?php

namespace App\Http\Controllers\api\dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasienRalanController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        $message = 'Seluruh Pasien Rawat Jalan berhasil dimuat';
        $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
            ->where('kd_dokter', $this->payload->get('sub'))
            ->where('status_lanjut', 'Ralan')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }

    public function now()
    {
        $message = 'Pasien Rawat Jalan hari ini berhasil dimuat';
        
        $spesialis = \App\Models\Dokter::getSpesialis($this->payload->get('sub'));
        $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
            ->where('kd_dokter', $this->payload->get('sub'))
            ->where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ralan')
            ->orderByRaw("FIELD(kd_poli, 'BBL', 'P001', 'P009', 'P007', 'LAB', 'OPE', 'U0016', 'P003', 'U0017', 'P008', 'P005', 'PKIA', 'P004', 'P006', 'IGDK', 'P002')");
            
        if(str_contains(strtolower($spesialis->nm_sps), 'anak')) {
            $pasien->whereHas('poliklinik', function($query) {
                $query->whereNotIn('nm_poli', ['IGDK', 'UGD']);
            });
        }
            
        $pasien = $pasien->paginate(env('PER_PAGE', 20));

        return isSuccess($pasien, $message);
    }

    function byDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $message = "Pasien Rawat Jalan tahun $tahun berhasil dimuat";
            $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null) {
            $message = "Pasien Rawat Jalan bulan $bulan tahun $tahun berhasil dimuat";
            $pasien  = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $message  = "Pasien Rawat Jalan tanggal $tanggal bulan $bulan tahun $tahun berhasil dimuat";
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien   = \App\Models\RegPeriksa::with(['pasien', 'penjab', 'poliklinik'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(env('PER_PAGE', 20));
        }

        return isSuccess($pasien, $message);
    }
}
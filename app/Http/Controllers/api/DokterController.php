<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    protected $payload;

    public function __construct()
    {
        $this->payload = auth()->payload();
    }

    public function index()
    {
        // get dokter by kd_dokter, kd_dokter get from token sub
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\Dokter::with(['pegawai', 'pegawai.kualifikasi_staff'])
            ->where('kd_dokter', $kd_dokter)
            ->first();

        return isSuccess($dokter, 'Data berhasil dimuat');
    }
    
    public function spesialis()
    {
        $kd_dokter = $this->payload->get('sub');
        $dokter = \App\Models\Dokter::select('spesialis.kd_sps', 'spesialis.nm_sps')
            ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
            ->where('dokter.kd_dokter', $kd_dokter)
            ->first();

        return isSuccess($dokter, 'Data berhasil dimuat');
    }
    
    public function pasien()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->orderBy('tgl_registrasi', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    public function pasienNow()
    {
        $kd_dokter = $this->payload->get('sub');

        $pasien = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->orderBy('jam_reg', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    function pasienByDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $pasien = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $pasien = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    public function pasienRawatJalan()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
            ->where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ralan')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    public function pasienRawatJalanNow()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
            ->where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ralan')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    function pasienRawatJalanByDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->where('status_lanjut', 'Ralan')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    public function pasienRawatInap()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
            ->where('kd_dokter', $kd_dokter)
            ->where('status_lanjut', 'Ranap')
            ->orderBy('tgl_registrasi', 'DESC')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    public function pasienRawatInapNow()
    {
        $kd_dokter = $this->payload->get('sub');
        $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
            ->where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->where('status_lanjut', 'Ranap')
            ->orderBy('jam_reg', 'DESC')
            ->paginate(10);

        return isSuccess($pasien, 'Data berhasil dimuat');
    }

    function pasienRawatInapByDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->where('status_lanjut', 'Ranap')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->where('status_lanjut', 'Ranap')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $pasien = \App\Models\RegPeriksa::with(['pasien', 'dokter'])
                ->where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->where('status_lanjut', 'Ranap')
                ->orderBy('tgl_registrasi', 'DESC')
                ->orderBy('jam_reg', 'DESC')
                ->paginate(10);
        }

        return isSuccess($pasien, 'Data berhasil dimuat');
    }
    
    public function jadwalOperasi()
    {
        $kd_dokter = $this->payload->get('sub');
        $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $kd_dokter)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(10);

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }
    
    public function jadwalOperasiNow()
    {
        $kd_dokter = $this->payload->get('sub');
        $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $kd_dokter)
            ->where('tanggal', date('Y-m-d'))
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->paginate(10);

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }
    
    function jadwalOperasiByDate($tahun = null, $bulan = null, $tanggal = null)
    {
        if ($tahun !== null) {
            $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $jadwal = \App\Models\BookingOperasi::where('kd_dokter', $this->payload->get('sub'))
                ->where('tanggal', $fullDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('jam_mulai', 'DESC')
                ->paginate(10);
        }

        return isSuccess($jadwal, 'Data berhasil dimuat');
    }
    
    public function kunjunganDokter()
    {
        $kd_dokter = $this->payload->get('sub');
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->paginate(10);

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }
    
    public function kunjunganDokterNow()
    {
        $kd_dokter = $this->payload->get('sub');
        $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $kd_dokter)
            ->where('tgl_registrasi', date('Y-m-d'))
            ->paginate(10);

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }

    function kunjunganDokterByDate($tahun = null, $bulan = null, $tanggal = null) {
        if ($tahun !== null) {
            $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null) {
            $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->whereYear('tgl_registrasi', $tahun)
                ->whereMonth('tgl_registrasi', $bulan)
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->paginate(10);
        }

        if ($tahun !== null && $bulan !== null && $tanggal !== null) {
            $fullDate = $tahun . '-' . $bulan . '-' . $tanggal;
            $kunjungan = \App\Models\RegPeriksa::where('kd_dokter', $this->payload->get('sub'))
                ->where('tgl_registrasi', $fullDate)
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->paginate(10);
        }

        return isSuccess($kunjungan, 'Data berhasil dimuat');
    }
}

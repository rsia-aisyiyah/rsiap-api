<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public function dokter()
    {
        return $this->hasOne(Dokter::class, 'kd_dokter', 'nik');
    }

    public function bidang()
    {
        return $this->hasOne(Bidang::class, 'nama', 'jnj_jabatan');
    }

    public function berkas()
    {
        return $this->hasMany(BerkasPegawai::class, 'nik', 'nik');
    }

    public function spkrkk()
    {
        return $this->hasMany(BerkasPegawai::class, 'nik', 'nik')->whereIn('kode_berkas', ['MBP0006', 'MBP0019', 'MBP0032', 'MBP0045'])->orderBy('tgl_uploud', 'desc');
    }

    public function bidang_detail()
    {
        return $this->hasOne(Bidang::class, 'nama', 'jnj_jabatan');
    }

    public function pendidikan()
    {
        return $this->hasOne(Pendidikan::class, 'tingkat', 'pendidikan');
    }

    public function resiko_kerja()
    {
        return $this->hasOne(ResikoKerja::class, 'kode_resiko', 'kode_resiko');
    }

    public function kelompok_jabatan()
    {
        return $this->hasOne(KelompokJabatan::class, 'kode_kelompok', 'kode_kelompok');
    }

    public function dpt()
    {
        return $this->hasOne(Departemen::class, 'dep_id', 'departemen');
    }

    public function petugas()
    {
        return $this->hasOne(Petugas::class, 'nip', 'nik');
    }

    public function kualifikasi_staff_klinis()
    {
        return $this->hasOne(KualifikasiStaff::class, 'nik', 'nik');
    }

    public function rsia_departemen_jm()
    {
        return $this->hasOne(RsiaDepartemenJm::class, 'nik', 'nik');
    }

    public function cuti()
    {
        return $this->hasMany(RsiaCuti::class, 'nik', 'nik');
    }

    public function diklat()
    {
        return $this->hasMany(RsiaDiklat::class, 'id_peg', 'id');
    }

    public function rsia_email_pegawai()
    {
        return $this->hasOne(EmailPegawai::class, 'nik', 'nik');
    }

    public function stts_kerja()
    {
        return $this->hasOne(StatusKerja::class, 'stts', 'stts_kerja');
    }

    public function jenjang_jabatan()
    {
        return $this->hasOne(JenjangJabatan::class, 'kode', 'jnj_jabatan');
    }
}

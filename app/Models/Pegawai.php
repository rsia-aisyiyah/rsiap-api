<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'id';

    public function dokter()
    {
        return $this->hasOne(Dokter::class, 'kd_dokter', 'nik');
    }

    public function bidang()
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

    public function kualifikasi_staff()
    {
        return $this->hasOne(KualifikasiStaff::class, 'nik', 'nik');
    }
}
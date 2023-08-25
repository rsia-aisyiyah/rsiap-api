<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;
    protected $table = 'petugas';

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'nik', 'nip');
    }

    public function jabatan()
    {
        return $this->hasOne(Jabatan::class, 'kd_jbtn', 'kd_jbtn');
    }

    public function periksaLab()
    {
        return $this->hasMany(PeriksaLab::class, 'nip', 'nip');
    }

    function grafikHarian() {
        return $this->hasMany(RsiaGrafikHarian::class, 'nip', 'nip');
    }
}

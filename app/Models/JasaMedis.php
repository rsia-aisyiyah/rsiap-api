<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaMedis extends Model
{
    use HasFactory;

    protected $table = 'rsia_log_jm_dokter';
    // protected $hidden = ['no_rkm_medis', 'no_ktp', 'no_peserta'];

    // public function jasaMedis()
    // {
    //     return $this->hasMany(Pegawai::class, 'kd_dokter', 'nik');
    // }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }
}

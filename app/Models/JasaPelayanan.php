<?php

namespace App\Models;

use App\Models\JasaPelayananAkun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JasaPelayanan extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;


    protected $table = 'rsia_log_jm';
    // protected $hidden = ['no_rkm_medis', 'no_ktp', 'no_peserta'];

    // public function jasaMedis()
    // {
    //     return $this->hasMany(Pegawai::class, 'kd_dokter', 'nik');
    // }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
    public function jasa_pelayanan_akun()
    {
        return $this->belongsTo(
            JasaPelayananAkun::class,
            ['tahun', 'bulan'],
            ['tahun', 'bulan']
        );
    }
}

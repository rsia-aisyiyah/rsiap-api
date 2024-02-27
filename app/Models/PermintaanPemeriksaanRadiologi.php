<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPemeriksaanRadiologi extends Model
{
    use HasFactory;

    protected $table = 'permintaan_pemeriksaan_radiologi';

    public $timestamps = false;


    public function jenis()
    {
        return $this->hasOne(
            JenisPerawatanRadiologi::class,
            'kd_jenis_prw',
            'kd_jenis_prw',
        );
    }

    // permintaan radiologi by noorder
    public function permintaan()
    {
        return $this->belongsTo(
            PermintaanRadiologi::class,
            'noorder',
            'noorder'
        );
    }
}

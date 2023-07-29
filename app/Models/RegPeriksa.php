<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegPeriksa extends Model
{
    use HasFactory;

    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    public $timestamps = false;

    protected $casts = [
        'no_rawat' => 'string',
    ];

    protected $hidden = [
        'kd_poli', 'kd_dokter'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function kamarInap()
    {
        return $this->hasMany(KamarInap::class, 'no_rawat', 'no_rawat');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function penjab()
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function pemeriksaanRalan()
    {
        return $this->hasOne(PemeriksaanRalan::class, 'no_rawat', 'no_rawat');
    }

    public function pemeriksaanRanap()
    {
        return $this->hasMany(PemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    public function bookingOperasi()
    {
        return $this->hasOne(BookingOperasi::class, 'no_rawat', 'no_rawat');
    }

    public function operasi()
    {
        return $this->hasMany(Operasi::class, 'no_rawat', 'no_rawat')->latest('tgl_operasi');
    }
}

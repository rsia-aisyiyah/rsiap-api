<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokter';
    protected $hidden = ['kd_dokter'];

    // custom function
    public static function getSpesialis($kd_dokter) 
    {
        $dokter = Dokter::select('spesialis.kd_sps', 'spesialis.nm_sps')
            ->join('spesialis', 'spesialis.kd_sps', '=', 'dokter.kd_sps')
            ->where('dokter.kd_dokter', $kd_dokter)
            ->first();

        return $dokter;
    }


    public function regPeriksa()
    {
        return $this->hasMany(RegPeriksa::class, 'kd_dokter', 'kd_dokter');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }

    function booking_operasi()
    {
        return $this->hasMany(BookingOperasi::class, 'kd_dokter', 'kd_dokter');
    }

    public function spesialis()
    {
        return $this->belongsTo(Spesialis::class, 'kd_sps', 'kd_sps');
    }
}
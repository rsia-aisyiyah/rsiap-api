<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanRadiologi extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $table = 'permintaan_radiologi';

    public $timestamps = false;

    public function hasil()
    {
        return $this->hasOne(
            HasilRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_hasil', 'jam_hasil']
        );
    }

    public function periksa()
    {
        return $this->belongsTo(
            PeriksaRadiologi::class,
            ['no_rawat', 'tgl_hasil', 'jam_hasil'],
            ['no_rawat', 'tgl_periksa', 'jam'],
        );
    }

    public function gambar()
    {
        return $this->hasMany(
            GambarRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_hasil', 'jam_hasil']
        );
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat');
    }
}

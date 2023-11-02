<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriksaRadiologi extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $table = 'periksa_radiologi';

    public $timestamps = false;

    public function hasil()
    {
        return $this->hasMany(
            HasilRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_periksa', 'jam']
        );
    }

    // reg periksa by no rawat
    public function regPeriksa()
    {
        return $this->belongsTo(
            RegPeriksa::class,
            'no_rawat', 'no_rawat'
        );
    }
}

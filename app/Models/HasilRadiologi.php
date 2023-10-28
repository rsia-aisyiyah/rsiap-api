<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilRadiologi extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $table = 'hasil_radiologi';

    public $timestamps = false;

    public function permintaan()
    {
        return $this->belongsTo(
            PermintaanRadiologi::class,
            ['no_rawat', 'tgl_hasil', 'jam_hasil'],
            ['no_rawat', 'tgl_periksa', 'jam']
        );
    }

    public function gambar()
    {
        return $this->hasMany(
            GambarRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_periksa', 'jam']
        );
    }
}

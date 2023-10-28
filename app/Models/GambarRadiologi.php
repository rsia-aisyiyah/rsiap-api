<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GambarRadiologi extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $table = 'gambar_radiologi';

    public $timestamps = false;

    public function hasil()
    {
        return $this->belongsTo(
            HasilRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_periksa', 'jam'],
        );
    }
}

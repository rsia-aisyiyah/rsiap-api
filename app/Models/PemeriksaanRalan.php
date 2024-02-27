<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanRalan extends Model
{
    use HasFactory;
    protected $table = 'pemeriksaan_ralan';
    protected $primaryKey = 'no_rawat';
    public $timestamps = false;

    protected $casts = [
        'no_rawat' => 'string',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
    
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanRanap extends Model
{
    use HasFactory;
    protected $table = 'pemeriksaan_ranap';
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

    public function verifikasi()
    {
        return $this->hasMany(RsiaVerifPemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }
}

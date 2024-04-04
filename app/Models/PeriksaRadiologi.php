<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriksaRadiologi extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $table = 'periksa_radiologi';
    
    protected $primaryKey = 'no_rawat';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
    
    protected $casts = [
        'no_rawat' => 'string',
    ];


    public function hasil()
    {
        return $this->belongsTo(
            HasilRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_periksa', 'jam']
        );
    }

    public function jenis()
    {
        return $this->hasOne(
            JenisPerawatanRadiologi::class,
            'kd_jenis_prw',
            'kd_jenis_prw',
        );
    }

    public function permintaan()
    {
        return $this->belongsTo(
            PermintaanRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_hasil', 'jam_hasil']
        );
    }

    public function gambar()
    {
        return $this->belongsTo(
            GambarRadiologi::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_periksa', 'jam'],
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

    public function dokter()
    {
        return $this->belongsTo(
            Dokter::class,
            'dokter_perujuk', 
            'kd_dokter'
        );
    }

    public function petugas()
    {
        return $this->belongsTo(
            Petugas::class,
            'nip', 
            'nip'
        );
    }
}

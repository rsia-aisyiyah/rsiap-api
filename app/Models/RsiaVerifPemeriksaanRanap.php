<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaVerifPemeriksaanRanap extends Model
{
    use HasFactory;
    protected $table = 'rsia_verif_pemeriksaan_ranap';
    protected $fillable = ['no_rawat', 'tgl_perawatan', 'jam_rawat', 'tgl_verif', 'jam_verif', 'verifikator'];
    public $timestamps = false;
    protected $guarded = [];

    function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    function pemeriksaanRanap()
    {
        return $this->belongsTo(PemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }
    function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    function petugas()
    {
        return $this->belongsTo(Petugas::class, 'verifikator', 'nip');
    }

    function grafikHarian()
    {
        return $this->belongsTo(RsiaGrafikHarian::class, 'no_rawat', 'no_rawat');
    }
}
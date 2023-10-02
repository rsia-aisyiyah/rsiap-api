<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaGrafikHarian extends Model
{
    use HasFactory;
    protected $table = 'rsia_grafik_harian';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat','tgl_perawatan', 'jam_rawat', 'suhu_tubuh', 'tensi', 'nadi', 
        'respirasi', 'spo2', 'o2', 'gcs', 'kesadaran', 'sumber', 'nip',
    ];

    function petugas() {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    function verifikasi() {
        return $this->hasMany(RsiaVerifPemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }

    function pegawai() {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }
}

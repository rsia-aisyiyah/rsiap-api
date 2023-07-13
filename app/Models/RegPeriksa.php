<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegPeriksa extends Model
{
    use HasFactory;

    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    protected $hidden = ['kd_dokter', 'no_rkm_medis', 'no_reg'];

    
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
    
    public function kamarInap()
    {
        return $this->hasMany(KamarInap::class, 'no_rawat', 'no_rawat');
    }
    
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    // public function ranapGabung()
    // {
    //     return $this->belongsTo(RanapGabung::class, 'no_rawat', 'no_rawat');
    // }
    
    
    // public function upload()
    // {
    //     return $this->hasMany(Upload::class, 'no_rawat', 'no_rawat');
    // }
    
    
    // public function pemeriksaanRalan()
    // {
    //     return $this->hasOne(PemeriksaanRalan::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function catatanPerawatan()
    // {
    //     return $this->hasOne(CatatanPerawatan::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function poliklinik()
    // {
    //     return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    // }
    
    // public function penjab()
    // {
    //     return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    // }
    
    // public function diagnosaPasien()
    // {
    //     return $this->hasMany(DiagnosaPasien::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function detailPemberianObat()
    // {
    //     return $this->hasMany(DetailPemberianObat::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function detailPemeriksaanLab()
    // {
    //     return $this->hasMany(DetailPemeriksaanLab::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function prosedurPasien()
    // {
    //     return $this->hasMany(ProsedurPasien::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function medisRalanKandungan()
    // {
    //     return $this->hasMany(PenilaianMedisRalanKandungan::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function resepObat()
    // {
    //     return $this->hasMany(ResepObat::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function generalConsent()
    // {
    //     return $this->hasOne(RsiaGeneralConsent::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function askepRalanKebidanan()
    // {
    //     return $this->hasMany(AskepRalanKebidanan::class, 'no_rawat', 'no_rawat');
    // }
    
    // public function askepRalanAnak()
    // {
    //     return $this->hasMany(AskepRalanAnak::class, 'no_rawat', 'no_rawat');
    // }
}
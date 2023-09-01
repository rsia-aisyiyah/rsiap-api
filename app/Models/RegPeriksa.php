<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegPeriksa extends Model
{
    use HasFactory;

    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    public $timestamps = false;

    protected $casts = [
        'no_rawat' => 'string',
    ];

    protected $hidden = [
        'kd_poli', 'kd_dokter'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function kamarInap()
    {
        return $this->belongsTo(KamarInap::class, 'no_rawat', 'no_rawat');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function penjab()
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function pemeriksaanRalan()
    {
        return $this->hasOne(PemeriksaanRalan::class, 'no_rawat', 'no_rawat');
    }

    public function pemeriksaanRanap()
    {
        return $this->hasMany(PemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    public function bookingOperasi()
    {
        return $this->hasOne(BookingOperasi::class, 'no_rawat', 'no_rawat');
    }

    public function operasi()
    {
        return $this->hasMany(Operasi::class, 'no_rawat', 'no_rawat')->latest('tgl_operasi');
    }

    // bridging sep
    public function bridgingSep()
    {
        return $this->hasOne(BridgingSep::class, 'no_rawat', 'no_rawat');
    }

    // data_triase_igd
    public function dataTriaseIgd()
    {
        return $this->hasOne(DataTriaseIgd::class, 'no_rawat', 'no_rawat');
    }

    // rsia_general_consent
    public function rsiaGeneralConsent()
    {
        return $this->hasOne(RsiaGeneralConsent::class, 'no_rawat', 'no_rawat');
    }

    // penilaianAwalKeperawatanIgd
    public function penilaianAwalKeperawatanIgd()
    {
        return $this->hasOne(PenilaianAwalKeperawatanIgd::class, 'no_rawat', 'no_rawat');
    }

    // PenilaianAwalKeperawatanKebidanan
    public function penilaianAwalKeperawatanKebidanan()
    {
        return $this->hasOne(PenilaianAwalKeperawatanKebidanan::class, 'no_rawat', 'no_rawat');
    }

    // PenilaianMedisIgd
    public function penilaianMedisIgd()
    {
        return $this->hasOne(PenilaianMedisIgd::class, 'no_rawat', 'no_rawat');
    }

    // TransferPasienAntarRuang
    public function transferPasienAntarRuang()
    {
        return $this->hasOne(TransferPasienAntarRuang::class, 'no_rawat', 'no_rawat');
    }

    // ResepObat
    public function resepObat()
    {
        return $this->hasOne(ResepObat::class, 'no_rawat', 'no_rawat');
    }

    
}

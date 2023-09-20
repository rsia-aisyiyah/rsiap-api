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
        'no_rkm_medis' => 'string'
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

    // RsiaVerifPemeriksaanRanap
    public function rsiaVerifPemeriksaanRanap()
    {
        return $this->hasOne(RsiaVerifPemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }

    // no_rawat
    public function grafikHarian()
    {
        return $this->hasMany(RsiaGrafikHarian::class, 'no_rawat', 'no_rawat');
    }

    // skriningGizi
    public function skriningGizi()
    {
        return $this->hasOne(RsiaSkriningGizi::class, 'no_rawat', 'no_rawat');  
    }

    // rekonsiliasiObat
    public function rekonsiliasiObat()
    {
        return $this->hasOne(RekonsiliasiObat::class, 'no_rawat', 'no_rawat');
    }

    public function resumePasienRanap()
    {
        return $this->hasOne(ResumePasienRanap::class, 'no_rawat', 'no_rawat');
    }

    // penilaian medis ralan anak
    public function penilaianMedisRalanAnak()
    {
        return $this->hasOne(PenilaianMedisRalanAnak::class, 'no_rawat', 'no_rawat');
    }

    // penilaian medis ralan kandungan
    public function penilaianMedisRalanKandungan()
    {
        return $this->hasOne(PenilaianMedisRalanKandungan::class, 'no_rawat', 'no_rawat');
    }

    public function ranapDokter()
    {
        return $this->hasOne(RawatInapDr::class, 'no_rawat', 'no_rawat');
    }

    public function ranapGabungan()
    {
        return $this->hasOne(RawatInapDrPr::class, 'no_rawat', 'no_rawat');
    }

    public function ralanDokter()
    {
        return $this->hasOne(RawatJalanDr::class, 'no_rawat', 'no_rawat');
    }

    public function ralanGabungan()
    {
        return $this->hasOne(RawatJalanDrPr::class, 'no_rawat', 'no_rawat');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaNotulen extends Model
{
    use HasFactory;

    protected $table = 'rsia_notulen';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;


    // notulis to pegawai.nik
    public function notulis()
    {
        return $this->hasOne(Pegawai::class, 'nik', 'notulis_nik');
    }

    // surat / undangan no_surat to rsia_surat_internal.no_surat
    public function surat()
    {
        return $this->belongsTo(RsiaSuratInternal::class, 'no_surat', 'no_surat');
    }

    // peserta
    public function peserta()
    {
        return $this->hasMany(RsiaSuratInternalPenerima::class, 'no_surat', 'no_surat');
    }

    // 
}

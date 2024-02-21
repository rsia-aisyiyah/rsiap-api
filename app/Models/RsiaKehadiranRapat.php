<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaKehadiranRapat extends Model
{
    use HasFactory;

    protected $table = 'rsia_kehadiran_rapat';

    protected $guarded = [];
    
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $casts = [
        'no_surat' => 'string',
        'nik' => 'string',
    ];


    // pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }

    // surat
    public function surat()
    {
        return $this->belongsTo(RsiaSuratInternal::class, 'no_surat', 'no_surat');
    }

    // notulen
    public function notulen()
    {
        return $this->belongsTo(RsiaNotulen::class, 'no_surat', 'no_surat');
    }

    // penerima
    public function penerima()
    {
        return $this->belongsTo(RsiaSuratInternalPenerima::class, 'no_surat', 'no_surat');
    }
}

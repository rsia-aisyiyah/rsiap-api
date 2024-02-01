<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSuratInternalPenerima extends Model
{
    use HasFactory;

    protected $table = 'rsia_surat_internal_penerima';

    protected $guarded = [];

    public $timestamps = false;


    protected $casts = [
        'no_surat' => 'string'
    ];

    public function petugas()
    {
        return $this->hasOne(Petugas::class, 'nip', 'penerima');
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'nik', 'penerima');
    }

    // surat
    public function surat()
    {
        return $this->belongsTo(RsiaSuratInternal::class, 'no_surat', 'no_surat');
    }
}

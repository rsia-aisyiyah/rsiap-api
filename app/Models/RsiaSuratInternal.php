<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSuratInternal extends Model
{
    use HasFactory;

    protected $table = 'rsia_surat_internal';

    protected $guarded = [];

    protected $primaryKey = 'no_surat';

    public $timestamps = false;

    
    
    protected $casts = [
        'no_surat' => 'string'
    ];

    public function pj_detail()
    {
        return $this->hasOne(Petugas::class, 'nip', 'pj');
    }

    public function pegawai_detail()
    {
        return $this->hasOne(Pegawai::class, 'nik', 'pj');
    }

    public function penerima()
    {
        return $this->hasMany(RsiaSuratInternalPenerima::class, 'no_surat', 'no_surat');
    }

    public function memo()
    {
        return $this->hasOne(RsiaMemoInternal::class, 'no_surat', 'no_surat');
    }

    public function penanggung_jawab()
    {
        return $this->pegawai_detail();
    }
}

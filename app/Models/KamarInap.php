<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarInap extends Model
{
    use HasFactory;

    protected $table = 'kamar_inap';

    protected $primaryKey = 'no_rawat';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
    
    protected $casts = [
        'no_rawat' => 'string',
    ];

    protected $guarded = [];
    

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function ranapGabung()
    {
        return $this->belongsTo(RanapGabung::class, 'no_rawat', 'no_rawat');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kd_kamar', 'kd_kamar');
    }

    // pasien from reg_periksa
    public function pasien()
    {
        return $this->hasOneThrough(Pasien::class, RegPeriksa::class, 'no_rawat', 'no_rkm_medis', 'no_rawat', 'no_rkm_medis');
    }

    public function sep()
    {
        return $this->hasOne(BridgingSep::class, 'no_rawat', 'no_rawat')->select(['no_rawat', 'no_sep', 'tglsep', 'diagawal', 'klsrawat']);
    }
    
    public function inacbg()
    {
        return $this->hasOneThrough(InacbgGroupStage12::class, BridgingSep::class, 'no_rawat', 'no_sep', 'no_rawat', 'no_sep');
    }
}

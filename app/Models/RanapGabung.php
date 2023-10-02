<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanapGabung extends Model
{
    use HasFactory;

    protected $table = 'ranap_gabung';

    function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat2', 'no_rawat');
    }
    
    function kamarInap()
    {
        return $this->belongsTo(KamarInap::class, 'no_rawat', 'no_rawat');
    }
}

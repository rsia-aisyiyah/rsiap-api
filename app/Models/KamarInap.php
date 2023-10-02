<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarInap extends Model
{
    use HasFactory;

    protected $table = 'kamar_inap';

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

    public function ranapGabung()
    {
        return $this->belongsTo(RanapGabung::class, 'no_rawat', 'no_rawat');
    }
}

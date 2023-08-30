<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaCuti extends Model
{
    use HasFactory;

    protected $table = 'rsia_cuti';

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }

    public function bersalin()
    {
        return $this->hasOne(RsiaCutiBersalin::class, 'id_cuti', 'id_cuti');
    }
}
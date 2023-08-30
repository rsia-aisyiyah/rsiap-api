<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaDiklat extends Model
{
    use HasFactory;

    protected $table = 'rsia_diklat';

    // pegawai
    public function pegawai() {
        return $this->hasOne(Pegawai::class, 'nik', 'nik');
    }

    public function kegiatan() {
        return $this->hasOne(RsiaKegiatan::class, 'id', 'id_kegiatan');
    }
}

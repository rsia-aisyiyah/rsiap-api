<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'rsia_kegiatan';

    public function diklat() {
        return $this->hasOne(RsiaDiklat::class, 'id_kegiatan', 'id');
    }
}

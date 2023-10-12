<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poliklinik extends Model
{
    use HasFactory;
    protected $table = 'poliklinik';

    public function regPeriksa()
    {
        return $this->hasMany(RegPeriksa::class, 'kd_poli', 'kd_poli');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kd_poli', 'kd_poli');
    }
}

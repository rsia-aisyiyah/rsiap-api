<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;
    protected $table = 'petugas';

    public function periksaLab()
    {
        return $this->hasMany(PeriksaLab::class, 'nip', 'nip');
    }

    function grafikHarian() {
        return $this->hasMany(RsiaGrafikHarian::class, 'nip', 'nip');
    }
}

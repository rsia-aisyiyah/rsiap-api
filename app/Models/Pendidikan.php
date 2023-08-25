<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikan';

    // pendidikan
    public function pegawai()
    {
        // pendidikan field is tingkat connected to status_koor in table pegawai
        return $this->hasMany(Pegawai::class, 'pendidikan', 'tingkat');
    }
}

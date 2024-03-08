<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokJabatan extends Model
{
    use HasFactory;

    protected $table = 'kelompok_jabatan';

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'kode_kelompok', 'kode_kelompok');
    }
}
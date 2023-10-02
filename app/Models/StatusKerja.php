<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKerja extends Model
{
    use HasFactory;

    protected $table = 'stts_kerja';

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'stts_kerja', 'stts');
    }
}

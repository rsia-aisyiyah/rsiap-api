<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    public function petugas()
    {
        return $this->hasMany(Petugas::class, 'kd_jbtn', 'kd_jbtn');
    }
}

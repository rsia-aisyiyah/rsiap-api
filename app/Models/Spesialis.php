<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spesialis extends Model
{
    use HasFactory;

    protected $table = 'spesialis';

    public function dokter()
    {
        return $this->hasMany(Dokter::class, 'kd_sps', 'kd_sps');
    }
}

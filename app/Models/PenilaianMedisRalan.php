<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianMedisRalan extends Model
{
    use HasFactory;

    protected $table = 'penilaian_medis_ralan';

    public $timestamps = false;
}

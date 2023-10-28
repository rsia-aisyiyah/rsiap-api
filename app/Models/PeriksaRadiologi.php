<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriksaRadiologi extends Model
{
    use HasFactory;

    protected $table = 'periksa_radiologi';

    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaDepartemenJm extends Model
{
    use HasFactory;

    protected $table = 'rsia_departemen_jm';
    protected $primaryKey = 'id_jm';

    protected $guarded = [];

    public $timestamps = false;
}

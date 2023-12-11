<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSpo extends Model
{
    use HasFactory;

    protected $table = 'rsia_spo';

    protected $guarded = [];

    protected $primaryKey = 'nomor';

    public $incrementing = false;

    public $timestamps = false;
}

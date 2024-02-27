<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSpoDetail extends Model
{
    use HasFactory;

    protected $table = 'rsia_spo_detail';

    protected $guarded = [];

    protected $primaryKey = 'nomor';

    public $incrementing = false;

    public $timestamps = false;

}

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

    // unit to departemen
    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'dep_id', 'unit');
    }

    public function detail()
    {
        return $this->hasOne(RsiaSpoDetail::class, 'nomor', 'nomor');
    }
}

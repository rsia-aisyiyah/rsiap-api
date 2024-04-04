<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InacbgGroupStage12 extends Model
{
    use HasFactory;

    protected $table = 'inacbg_grouping_stage12';

    protected $primaryKey = 'no_sep';

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    public function sep()
    {
        return $this->belongsTo(BridgingSep::class, 'no_sep', 'no_sep');
    }
}

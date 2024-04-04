<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BridgingSep extends Model
{
    use HasFactory;

    protected $table = 'bridging_sep';

    protected $primaryKey = 'no_sep';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;


    public function kamarInap()
    {
        return $this->belongsTo(KamarInap::class, 'no_rawat', 'no_rawat');
    }

    public function inacbg()
    {
        return $this->belongsTo(InacbgGroupStage12::class, 'no_sep', 'no_sep');
    }
}

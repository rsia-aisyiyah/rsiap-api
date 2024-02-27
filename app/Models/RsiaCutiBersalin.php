<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaCutiBersalin extends Model
{
    use HasFactory;

    protected $table = 'rsia_cuti_bersalin';
    protected $guarded = [];
    public $timestamps = false;

    public function cuti()
    {
        return $this->belongsTo(RsiaCuti::class, 'id_cuti', 'id_cuti');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaPelayananAkun extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $table = 'rsia_jm_copy';

    // public function jasaPelayanan(){
    //     return $this->belongsTo(
    //         JasaPelayanan::class,
    //         ['tahun', 'bulan'],
    //         ['tahun', 'bulan']
    //     );
    // }

}


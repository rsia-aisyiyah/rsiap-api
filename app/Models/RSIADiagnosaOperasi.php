<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RSIADiagnosaOperasi extends Model
{
    use HasFactory;

    protected $table = 'rsia_diagnosa_operasi';

    protected $hidden = ['no_rawat'];

    public function bookingOperasi()
    {
        return $this->belongsTo(BookingOperasi::class, 'no_rawat', 'no_rawat');
    }
}

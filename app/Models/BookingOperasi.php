<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingOperasi extends Model
{
    use HasFactory;

    protected $table = 'booking_operasi';
    protected $primaryKey = '';
    protected $hidden = ['kd_dokter'];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }
}

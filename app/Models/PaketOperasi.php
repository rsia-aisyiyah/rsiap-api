<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketOperasi extends Model
{
    use HasFactory;

    protected $table = 'paket_operasi';

    protected $hidden = ['kode_paket'];

    public function bookingOperasi()
    {
        return $this->hasMany(BookingOperasi::class, 'kode_paket', 'kode_paket');
    }
}

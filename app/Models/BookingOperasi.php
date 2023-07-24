<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingOperasi extends Model
{
    use HasFactory;

    protected $table = 'booking_operasi';
    protected $hidden = ['kd_dokter', 'kode_paket'];

    protected $casts = [
        'no_rawat' => 'string',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function paketOperasi()
    {
        return $this->belongsTo(PaketOperasi::class, 'kode_paket', 'kode_paket');
    }

    public function rsiaDiagnosaOperasi()
    {
        return $this->belongsTo(RSIADiagnosaOperasi::class, 'no_rawat', 'no_rawat');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}

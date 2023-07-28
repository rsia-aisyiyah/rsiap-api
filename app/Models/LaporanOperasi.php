<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanOperasi extends Model
{
    use HasFactory;

    protected $table = 'laporan_operasi';

    // Operasi
    public function operasi()
    {
        return $this->belongsTo(Operasi::class, 'no_rawat', 'no_rawat');
    }
}

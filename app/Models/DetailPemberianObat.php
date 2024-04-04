<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemberianObat extends Model
{
    use HasFactory;

    protected $table = 'detail_pemberian_obat';

    protected $primaryKey = 'no_rawat';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
    
    protected $casts = [
        'no_rawat' => 'string',
    ];

    public function obat()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');  
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBarang extends Model
{
    use HasFactory;

    protected $table = 'databarang';

    public $timestamps = false;

    public function detailPemberianObat()
    {
        return $this->hasMany(DetailPemberianObat::class, 'kode_brng', 'kode_brng');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';
    public $timestamps = false;

    public function bangsal()
    {
        return $this->hasOne(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }
}

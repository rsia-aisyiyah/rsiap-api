<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawatJalanPr extends Model
{
    use HasFactory;

    protected $table = 'rawat_jl_pr';

    protected $primaryKey = 'no_rawat';

    protected $keyType = 'string';
    
    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'no_rawat' => 'string',
    ];
}

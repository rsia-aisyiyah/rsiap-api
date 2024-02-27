<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexIns extends Model
{
    use HasFactory;

    protected $table = 'indexins';

    public $timestamps = false;

    protected $fillable = [
        'dep_id', 'persen'
    ];
}

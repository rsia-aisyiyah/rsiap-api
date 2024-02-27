<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackerSql extends Model
{
    use HasFactory;
    
    protected $table = 'trackersql';
    protected $fillable = ['tanggal', 'sqle', 'usere'];
    public $timestamps = false;
}

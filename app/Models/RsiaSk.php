<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSk extends Model
{
    use HasFactory;

    protected $table = 'rsia_sk';
    
    protected $primaryKey = 'nomor';
    
    protected $guarded = [];

    public $timestamps = false;

    
    public function penanggungjawab()
    {
        return $this->belongsTo(Petugas::class, 'pj', 'nip')->select('nip', 'nama', 'kd_jbtn');
    }
}

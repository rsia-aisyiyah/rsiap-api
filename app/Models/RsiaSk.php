<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Thiagoprz\CompositeKey\HasCompositeKey;

class RsiaSk extends Model
{
    use HasCompositeKey, HasFactory;

    protected $table = 'rsia_sk';
    
    protected $primaryKey = ['nomor', 'jenis', 'tgl_terbit'];
    
    protected $guarded = [];
    
    public $timestamps = false;
    
    public $incrementing = false;
    


    public function penanggungjawab()
    {
        return $this->belongsTo(Petugas::class, 'pj', 'nip')->select('nip', 'nama', 'kd_jbtn');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class RsiaSuratPPI extends Model
{
    use HasCompositeKey, HasFactory;

    protected $table = 'rsia_surat_ppi';
    
    protected $primaryKey = ['nomor', 'tgl_terbit'];

    protected $guarded = [];

    public $timestamps = false;


    public function penanggungjawab()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik');
    }
}

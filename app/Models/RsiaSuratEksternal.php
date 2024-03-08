<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSuratEksternal extends Model
{
    use HasFactory;

    protected $table = 'rsia_surat_eksternal';

    protected $guarded = [];

    protected $primaryKey = 'no_surat';

    public $timestamps = false;



    protected $casts = [
        'no_surat' => 'string'
    ];

    public function pj_detail()
    {
        return $this->hasOne(Petugas::class, 'nip', 'pj');
    }
}

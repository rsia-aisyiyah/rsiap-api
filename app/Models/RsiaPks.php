<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaPks extends Model
{
    use HasFactory;

    protected $table = 'rsia_pks';

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $casts = [
        'no_pks_internal' => 'string',
        'no_pks_eksternal' => 'string',
    ];

    public function pj_detail()
    {
        return $this->hasOne(Pegawai::class, 'nik', 'pj');
    }
}

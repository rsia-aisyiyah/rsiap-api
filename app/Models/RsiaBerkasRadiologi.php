<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class RsiaBerkasRadiologi extends Model
{
    use HasFactory, HasCompositeKey;

    protected $table = 'rsia_berkas_radiologi';

    protected $primaryKey = ['nomor', 'tgl_terbit'];

    protected $guarded = [];

    public $timestamps = false;


    public function penanggungjawab()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik');
    }
}

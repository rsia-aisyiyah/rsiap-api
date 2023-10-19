<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailPegawai extends Model
{
    use HasFactory;

    protected $table = 'rsia_email_pegawai';
    protected $guarded = [];
    public $timestamps = false;


    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
}

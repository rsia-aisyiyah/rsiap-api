<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasPegawai extends Model
{
    use HasFactory;

    protected $table = 'berkas_pegawai';

    protected $guarded = [];

    public $timestamps = false;


    
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'nik', 'nik');
    }
    public function master_berkas_pegawai()
    {
        return $this->hasOne(MasterBerkasPegawai::class, 'kode', 'kode_berkas')->orderBy('master_berkas_pegawai.no_urut', 'asc');
    }
}

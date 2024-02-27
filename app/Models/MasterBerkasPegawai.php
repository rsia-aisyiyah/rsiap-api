<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBerkasPegawai extends Model
{
    use HasFactory;

    protected $table = 'master_berkas_pegawai';

    public function berkas_pegawai()
    {
        return $this->hasOne(BerkasPegawai::class, 'kode_berkas', 'kode');
    }

}

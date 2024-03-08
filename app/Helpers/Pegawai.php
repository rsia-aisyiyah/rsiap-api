<?php 

namespace App\Helpers;

class Pegawai
{
    // get me by nik
    public static function getMe($nik)
    {
        return \App\Models\Pegawai::where('nik', $nik)->first();
    }
}
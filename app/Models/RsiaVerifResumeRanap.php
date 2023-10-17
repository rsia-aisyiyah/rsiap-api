<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaVerifResumeRanap extends Model
{
    use HasFactory;

    protected $table = 'rsia_verif_resume_ranap';

    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tgl_verif',
        'jam_verif',
        'verifikator'
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    function petugas()
    {
        return $this->belongsTo(Petugas::class, 'verifikator', 'nip');
    }

    // resume
    public function resume()
    {
        return $this->belongsTo(ResumePasienRanap::class, 'no_rawat', 'no_rawat');
    }
}

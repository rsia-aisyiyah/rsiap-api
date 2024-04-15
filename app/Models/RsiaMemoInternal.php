<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaMemoInternal extends Model
{
    use HasFactory;

    protected $table = 'rsia_memo_internal';

    protected $guarded = [];

    public $timestamps = false;


    public function perihal()
    {
        return $this->belongsTo(RsiaSuratInternal::class, 'no_surat', 'no_surat');
    }

    // penerima lebih dari 1
    public function penerima()
    {
        return $this->hasMany(RsiaPenerimaUndangan::class, 'no_surat', 'no_surat');
    }
}

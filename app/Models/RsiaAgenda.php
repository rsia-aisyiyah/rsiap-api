<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaAgenda extends Model
{
    use HasFactory;

    protected $table = 'rsia_agenda';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public $timestamps = false;
    
    
    protected $with = ['Petugas'];

    public function Petugas()
    {
        return $this->belongsTo(Petugas::class, 'pj', 'nip')->select('nip', 'nama');
    }
}

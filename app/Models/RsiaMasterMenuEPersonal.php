<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaMasterMenuEPersonal extends Model
{
    use HasFactory;

    protected $table = 'rsia_master_menu_epersonal';

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $hidden = ['id', 'status'];

    public $timestamps = false;

    public $incrementing = true;

    protected $casts = [
        'url' => 'string',
    ];
}

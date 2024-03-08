<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetMenuEPersonal extends Model
{
    use HasFactory;

    protected $table = 'rsia_set_menu_epersonal';

    protected $guarded = ['id'];


    public function menu()
    {
        return $this->belongsTo(
            RsiaMasterMenuEPersonal::class,
            'menu_id',
            'id'
        );
    }
}

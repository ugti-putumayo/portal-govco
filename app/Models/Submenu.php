<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function subsubmenus()
    {
        return $this->hasMany(Subsubmenu::class)->orderBy('order');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subsubmenu extends Model
{
    public function submenu()
    {
        return $this->belongsTo(Submenu::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'route', 'order'];

    public function submenus()
    {
        return $this->hasMany(Submenu::class)->orderBy('order');
    }
}
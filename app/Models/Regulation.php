<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    use HasFactory;

    protected $table = 'regulations';

    protected $fillable = ['expedition_date', 'name', 'tipo', 'theme', 'link'];
}

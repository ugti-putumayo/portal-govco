<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transparency extends Model
{
    use HasFactory;
    protected $table = 'transparencia';

    /* public function subElementos()
    {
        return $this->hasMany(Transparency::class, 'id_padre')->where('tipo', 'subelemento')->orderBy('orden');
    } */
}

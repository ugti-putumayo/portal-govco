<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuestal extends Model
{
    use HasFactory;
    protected $table = 'presupuestals';

    /**
     * Asegura que 'expedition_date' se maneje como un objeto de fecha.
     */
    protected $casts = [
        'expedition_date' => 'date',
    ];
}
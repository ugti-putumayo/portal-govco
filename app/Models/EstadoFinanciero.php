<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoFinanciero extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla en la base de datos.
     */
    protected $table = 'estados_financieros';

    /**
     * Asegura que 'expedition_date' se maneje como un objeto de fecha.
     */
    protected $casts = [
        'expedition_date' => 'date',
    ];
}
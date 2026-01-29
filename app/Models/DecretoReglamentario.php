<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecretoReglamentario extends Model
{
    use HasFactory;

    protected $table = 'decreto_unico_reglamentario';

    protected $fillable = [
        'decreto_aplicable',
        'objetivo',
        'ambitos_regulados',
        'obligaciones',
        'cumplimiento_evaluacion',
        'documentos_politicas_relacionadas',
        'actualizaciones',
        'consultas_ciudadanas'
    ];
}

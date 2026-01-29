<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decision extends Model
{
    use HasFactory;

    protected $table = 'decisions';  // Nombre de la tabla

    // Definir los campos que se pueden asignar en masa
    protected $fillable = ['entry_date', 'name', 'description', 'archive'];
}

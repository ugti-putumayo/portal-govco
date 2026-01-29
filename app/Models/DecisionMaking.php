<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecisionMaking extends Model
{
    use HasFactory;

    protected $table = 'decision_making'; // Asegúrate de que el nombre coincida con el nombre de tu tabla

    // Define los campos que pueden ser asignados masivamente
    protected $fillable = ['title', 'description', 'responsible', 'status', 'link']; // Ajusta estos campos según tu tabla
}

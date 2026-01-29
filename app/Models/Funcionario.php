<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $table = 'funcionarios'; // Nombre de la tabla
    protected $fillable = ['nombres', 'apellidos', 'cargo', 'dependencias']; // Ajusta los campos según tu tabla
}

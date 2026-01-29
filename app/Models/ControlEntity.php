<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlEntity extends Model
{
    use HasFactory;

    protected $table = 'control_entities';

    // Asegúrate de que los campos aquí coincidan con los de tu tabla
    protected $fillable = ['name', 'tipo', 'mail', 'link'];

    // Si tu tabla tiene campos `created_at` y `updated_at`, Laravel los gestionará automáticamente,
    // pero si no los tiene, puedes deshabilitar los timestamps:
    public $timestamps = false;  // Si no tienes campos de timestamps en tu tabla
}

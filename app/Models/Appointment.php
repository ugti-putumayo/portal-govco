<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type', 
        'document_number', 
        'name', 
        'email', 
        'phone', 
        'address', 
        'date', 
        'hour', 
        'user_entity'
    ];
}

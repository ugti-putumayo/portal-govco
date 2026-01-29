<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantOfficial extends Model
{
    use HasFactory;    
    protected $table = 'plant_officials';
    protected $fillable = [
        'year_plantofficial',
        'month_plantofficial',
        'document_type',
        'document_number',
        'fullname',
        'charge',
        'dependency', 
        'subdependencie',
        'code',
        'grade',
        'level',
        'denomination',
        'total_value',
        'representation_expenses',
        'init_date',
        'vacation_date',
        'bonus_date',
        'email',
        'birthdate',
        'eps',
        'cellphone',
        'is_active'        
    ];
    public $timestamps = true;
}

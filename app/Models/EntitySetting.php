<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntitySetting extends Model
{
    use HasFactory;

    protected $table = 'entity_settings';
    protected $fillable = [
        'entity_name', 
        'entity_acronym', 
        'document_number', 
        'address', 
        'phone',
        'email', 
        'logo_path',
        'department',
        'city',
        'website',
        'legal_representative'
    ];
    public $timestamps = true;
}
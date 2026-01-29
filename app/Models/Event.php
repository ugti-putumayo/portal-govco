<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;    
    protected $table = 'events';
    protected $fillable = [
        'title',
        'description',
        'start',
        'end',
        'location',
        'image', 
        'is_public',
        'dependency',
        'visibility',
        'created_by',
        'updated_by'      
    ];
    public $timestamps = true;
}
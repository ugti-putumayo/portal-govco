<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Association extends Model
{
    use HasFactory;

    protected $table = 'associations';
    protected $fillable = [
        'name', 
        'classification', 
        'activity', 
        'description', 
        'sccope',
        'cellphone', 
        'email',
        'link',
        'image',
        'city',
        'address'
    ];
    public $timestamps = true;
}
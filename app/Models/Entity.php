<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;
    protected $table = 'entities';
    protected $fillable = [
        'type',
        'scope',
        'name', 
        'phone',
        'mail', 
        'address', 
        'link'
    ];
    public $timestamps = false;
}

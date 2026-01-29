<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    use HasFactory;
    protected $table = 'laws';
    protected $fillable = [
        'expedition_date', 
        'number', 
        'name', 
        'topic', 
        'link'
    ];
    public $timestamps = false;
}

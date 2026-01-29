<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePublication extends Model
{
    use HasFactory;
    protected $table = 'type_publications';
    protected $fillable = ['name', 'description'];
    public $timestamps = false;
}
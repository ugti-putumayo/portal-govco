<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'dependency_id', 
        'name', 
        'route'
    ];

    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }
}
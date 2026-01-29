<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';
    protected $fillable = ['name', 'lat', 'long', 'department_id'];
    public $timestamps = false;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
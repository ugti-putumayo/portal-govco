<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mipg;

class Dependency extends Model
{
    use HasFactory;
    protected $table = 'dependencies';
    protected $fillable = [
        'name', 
        'cellphone',
        'ext',
        'email',
        'address',
        'description',
        'image',
        'ubication',
        'shortname',
        'user_id'
    ];
    public $timestamps = true;

    public function subdependencies()
    {
        return $this->hasMany(Subdependency::class);
    }

    public function mipgItems()
    {
        return $this->hasMany(Mipg::class);
    }
}


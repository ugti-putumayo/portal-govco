<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dependency;

class Mipg extends Model
{
    use HasFactory;
    protected $table = 'mipg';
    protected $fillable = [
        'parent_id', 
        'name',
        'type',
        'file',
        'path',
        'extension',
        'dependency_id',
        'is_visible'
    ];
    public $timestamps = true;

    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }
}
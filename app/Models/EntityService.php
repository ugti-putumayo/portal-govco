<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityService extends Model
{
    use HasFactory;
    protected $table = 'entity_services';
    protected $fillable = [
        'title', 
        'slug',
        'icon',
        'description',
        'type_id',
        'url',
        'order_index',
        'state'
    ];
    public $timestamps = true;
}
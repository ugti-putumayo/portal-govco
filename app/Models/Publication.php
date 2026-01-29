<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'document',
        'link',
        'user_id',
        'type_id',
        'state',
        'date',
        'date_start',
        'date_end',
        'views'
    ];

    public $timestamps = false;

    protected $casts = [
        'date' => 'datetime',
        'date_start' => 'datetime',
        'date_end' => 'datetime',
        'state' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(TypePublication::class, 'type_id');
    }

    public function scopeActiveOfType($query, $typeId)
    {
        return $query->where('type_id', $typeId)->where('state', 1);
    }
}
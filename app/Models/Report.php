<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type_id',
        'year',
        'description',
        'document',
        'image',
        'dependency_id',
        'user_id',
        'state'
    ];

    public $timestamps = true;

    public function type()
    {
        return $this->belongsTo(ReportType::class, 'report_type_id');
    }

    public function scopeActiveOfType($query, $typeId)
    {
        return $query->where('report_type_id', $typeId)->where('state', 1);
    }
}
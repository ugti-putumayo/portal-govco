<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulatoryDecree extends Model
{
    use HasFactory;

    protected $table = 'regulatory_decree';

    protected $fillable = [
        'applicable_decree',
        'objective',
        'regulated_areas',
        'obligations',
        'compliance_evaluation',
        'related_policies',
        'updates',
        'citizen_consultations'
    ];
}
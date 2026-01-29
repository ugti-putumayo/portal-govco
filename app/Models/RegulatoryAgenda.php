<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulatoryAgenda extends Model
{
    use HasFactory;
    protected $table = 'regulatory_agenda';

    protected $fillable = [
        'title',
        'description',
        'estimated_date',
        'state',
        'document',
    ];
}
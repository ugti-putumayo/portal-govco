<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiringAnual extends Model
{
    use HasFactory;

    protected $table = 'hiring_anual';
    protected $fillable = [
        'name', 
        'tipo', 
        'archive'
    ];
}

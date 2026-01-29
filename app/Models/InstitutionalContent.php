<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionalContent extends Model
{
    use HasFactory;
    protected $table = 'institutional_contents';
    protected $fillable = [
        'content', 
        'order_index', 
        'is_active'
    ];
    public $timestamps = true;
}
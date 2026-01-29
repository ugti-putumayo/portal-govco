<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractual extends Model
{
    use HasFactory;
    protected $table = 'contractual';

    protected $fillable = [
        'expedition_date', 
        'name', 
        'type', 
        'object', 
        'link'];
}
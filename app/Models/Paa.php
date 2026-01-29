<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paa extends Model
{
    use HasFactory;

    protected $table = 'paa';

    protected $fillable = [
        'period',
        'name',
        'archive'
    ];
}

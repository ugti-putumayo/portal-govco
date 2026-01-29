<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovermentCalls extends Model
{
    use HasFactory;

    protected $table = 'communications'; // Ahora apunta a la tabla correcta
    protected $fillable = ['title', 'content', 'publication_date', 'attachment'];
}

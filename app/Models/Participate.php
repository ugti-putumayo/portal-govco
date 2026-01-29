<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participate extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image'];

    public function sections()
    {
        return $this->hasMany(ParticipateSection::class);
    }
}

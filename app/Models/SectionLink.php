<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionLink extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'title', 'url'];

    public function section()
    {
        return $this->belongsTo(ParticipateSection::class);
    }
}
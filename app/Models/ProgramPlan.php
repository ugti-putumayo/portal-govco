<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramPlan extends Model
{
    use HasFactory;

    protected $table = 'program_plans';

    protected $fillable = ['expedition_date', 'name', 'tipo', 'theme'];
}

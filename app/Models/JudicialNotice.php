<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudicialNotice extends Model
{
    use HasFactory;

    protected $table = 'judicial_notices';

    protected $fillable = [
        'tipo',
        'details',
        'publication_date',
        'link'
    ];
}

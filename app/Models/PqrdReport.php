<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PqrdReport extends Model
{
    use HasFactory;

    protected $table = 'pqrds_reports';

    protected $fillable = [
        'responsible_department',
        'document_type',
        'radicadas',
        'tramited',
        'medio_correo_electronico',
        'medio_correo_certificado',
        'medio_ventanilla',
        'medio_pqr_web',
        'year',
        'trimester',
    ];
}
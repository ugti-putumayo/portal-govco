<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;    
    protected $table = 'contractors';
    protected $fillable = [
        'year_contract',
        'month_contract',
        'contract_number',
        'date_contract',
        'code_secop',
        'class_contract', 
        'contractor',
        'firm_contractor',
        'process_modality',
        'object',
        'contract_term',
        'start_date',
        'cutoff_date',
        'total_value',
        'dependency',
        'link_secop',
        'supervision',
        'expense_class'        
    ];
    public $timestamps = true;
}

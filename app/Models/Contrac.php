<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrac extends Model
{
    use HasFactory;    
    protected $table = 'contracs';
    protected $fillable = [
        'contract_number', 
        'dependency',
        'contractor',
        'nit',
        'name',
        'objective',
        'subscription_date',
        'total_value',
        'duration',
        'time_addition',
        'total_time_with_addition',
        'start_date',
        'end_date',
        'contract_progress_percentage',
        'cutoff_date',
        'link_secop',
        'user_register_id'
    ];
    public $timestamps = true;

    public function getContractProgressPercentageAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $now = Carbon::now();

        if ($now->lt($start)) {
            return 0;
        }

        if ($now->gt($end)) {
            return 100;
        }

        $total = $start->diffInDays($end);
        $transcurridos = $start->diffInDays($now);

        if ($total === 0) {
            return 100;
        }

        return round(($transcurridos / $total) * 100);
    }
}

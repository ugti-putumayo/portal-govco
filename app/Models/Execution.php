<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Execution extends Model
{
    use HasFactory;
    protected $table = 'execution';
    protected $fillable = [
        'contract_number', 
        'dependency', 
        'contractor', 
        'nit', 
        'objective', 
        'subscription_date', 
        'total_value', 
        'duration', 
        'time_addition', 
        'start_date', 
        'end_date', 
        'contract_progress_percentage', 
        'cutoff_date'
    ];

    public function getProgressPercentageAttribute()
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $cutoffDate = $this->cutoff_date ? Carbon::parse($this->cutoff_date) : Carbon::now();
        $totalDuration = $endDate->diffInDays($startDate);
        $elapsedDuration = $cutoffDate->diffInDays($startDate);

        if ($elapsedDuration >= $totalDuration) {
            return 100;
        }

        $progressPercentage = ($elapsedDuration / $totalDuration) * 100;
        return round($progressPercentage, 2);
    }
}
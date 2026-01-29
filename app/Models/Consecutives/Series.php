<?php
namespace App\Models\Consecutives;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Consecutives\Consecutives;
use App\Models\Dependency;

class Series extends Model
{
    use HasFactory;
    protected $table = 'series';

    protected $fillable = [
        'name',
        'prefix',
        'dependency_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function counters(): HasMany
    {
        return $this->hasMany(Counter::class);
    }

    public function consecutives(): HasMany
    {
        return $this->hasMany(Consecutives::class);
    }

    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }
}
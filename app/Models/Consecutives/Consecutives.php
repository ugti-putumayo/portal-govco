<?php
namespace App\Models\Consecutives;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Person;

class Consecutives extends Model
{
    use HasFactory;
    protected $table = 'consecutives';

    protected $fillable = [
        'series_id',
        'user_id',
        'number',
        'year',
        'full_consecutive',
        'subject',
        'person_id',
        'recipient',
        'document_type',
        'internal_reference',
        'attachment_url',
        'notes',
        'status',
        'canceled_at',
        'canceled_by_user_id',
        'cancellation_reason',
    ];

    protected $casts = [
        'number' => 'integer',
        'year' => 'integer',
        'canceled_at' => 'datetime',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function canceledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by_user_id');
    }
}
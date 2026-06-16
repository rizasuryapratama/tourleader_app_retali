<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryItem extends Model
{
    protected $fillable = [
        'itinerary_day_id',
        'sequence',
        'start_time',
        'end_time',
        'title',
        'content',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id');
    }
}

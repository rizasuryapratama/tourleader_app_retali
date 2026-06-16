<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItineraryDay extends Model
{
    protected $fillable = [
        'itinerary_id',
        'day_number',
        'city',
        'date',
        'item_count',
    ];

    protected $casts = ['date' => 'date'];

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItineraryItem::class)->orderBy('sequence');
    }

    public function getStatusAttribute(): string
    {
        if (!$this->city || !$this->item_count) return 'empty';   
        if ($this->items->count() < $this->item_count) return 'incomplete'; 
        foreach ($this->items as $it) {
            if (!$it->title) return 'incomplete';
        }
        return 'complete'; 
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->status === 'complete';
    }
}
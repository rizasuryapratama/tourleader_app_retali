<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Itinerary extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'send_to',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
    public function days(): HasMany
    {
        return $this->hasMany(ItineraryDay::class)->orderBy('day_number');
    }

    public function tourLeaders(): BelongsToMany
    {
        return $this->belongsToMany(
            TourLeader::class,
            'itinerary_tour_leader',
            'itinerary_id',
            'tour_leader_id'
        )->withTimestamps();
    }

    public function muthawifs(): BelongsToMany
    {
        return $this->belongsToMany(
            Muthawif::class,
            'itinerary_muthawif',
            'itinerary_id',
            'muthawif_id'
        )->withTimestamps();
    }

    public function getRecipientLabelAttribute(): string
    {
        return match ($this->send_to) {
            'all_users'       => 'Semua Pengguna',
            'all_tourleaders' => 'Semua Tourleader',
            'all_muthawif'    => 'Semua Muthawif',
           'selected' => 'Pengguna Tertentu ('
                . $this->tourLeaders->count()
                . ' TL, '
                . $this->muthawifs->count()
                . ' Muthawif)',
            default           => '-',
        };
    }
    
    public function getIsCompleteAttribute(): bool
    {
        if ($this->days->isEmpty()) return false;

        foreach ($this->days as $day) {
            if (!$day->is_complete) return false;
        }

        return true;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Muthawif extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'muthawifs';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'kloter_id'
    ];

    protected $hidden = [
        'password', // 🔐 penting biar gak bocor ke API
    ];

    // =====================
    // RELATIONS
    // =====================

    public function kloter()
    {
        return $this->belongsTo(Kloter::class, 'kloter_id');
    }

    public function scans()
    {
        return $this->hasMany(Scan::class, 'muthawif_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(
            Task::class,
            'task_user',
            'muthawif_id',
            'task_id'
        )->withPivot('done_at')->withTimestamps();
    }

    public function checklistTasks()
    {
        return $this->belongsToMany(
            \App\Models\ChecklistTask::class,
            'checklist_task_user',
            'muthawif_id',
            'checklist_task_id'
        )->withPivot('done_at')->withTimestamps();
    }

    public function itineraries()
    {
        return $this->belongsToMany(
            \App\Models\Itinerary::class,
            'itinerary_muthawif',
            'muthawif_id',
            'itinerary_id'
        )->withTimestamps();
    }

    public function jamaah()
    {
        return $this->belongsToMany(Jamaah::class, 'jamaah_muthawif');
    }
}

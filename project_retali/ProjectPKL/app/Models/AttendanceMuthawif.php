<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMuthawif extends Model
{
    protected $fillable = [
        'muthawif_id',
        'foto',
        'latitude',
        'longitude',
    ];

    public function muthawif()
    {
        return $this->belongsTo(Muthawif::class);
    }
}

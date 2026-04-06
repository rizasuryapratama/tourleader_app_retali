<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PassportScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'passport_number',
        'owner_name',
        'owner_phone',
        'kloter',
        'tourleader_id',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function tourleader()
    {
        return $this->belongsTo(TourLeader::class, 'tourleader_id');
    }
}

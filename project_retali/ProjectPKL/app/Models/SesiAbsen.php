<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiAbsen extends Model
{
    use HasFactory;

    protected $table = 'sesi_absens';

    protected $fillable = [
        'judul',
    ];

    public function items()
    {
        return $this->hasMany(SesiAbsenItem::class, 'sesi_absen_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiAbsenItem extends Model
{
    protected $table = 'sesi_absen_items';

    protected $fillable = ['sesi_absen_id', 'isi'];

    public function sesiAbsen()
    {
        return $this->belongsTo(SesiAbsen::class);
    }
}
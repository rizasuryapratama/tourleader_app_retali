<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiJamaah extends Model
{
    protected $table = 'absensi_jamaah';

    protected $fillable = [
        'kloter_id',
        'judul_absen',
        'sesi_absen_id',
        'sesi_absen_item_id',
    ];

    /* ================= RELATIONS ================= */

    public function kloter()
    {
        return $this->belongsTo(Kloter::class);
    }

    public function sesiAbsen()
    {
        return $this->belongsTo(SesiAbsen::class);
    }

    public function sesiAbsenItem()
    {
        return $this->belongsTo(SesiAbsenItem::class);
    }

    public function jamaah()
    {
        return $this->hasMany(Jamaah::class, 'absen_id');
    }
}

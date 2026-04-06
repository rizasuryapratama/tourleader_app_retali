<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceJamaah extends Model
{
    protected $table = 'attendance_jamaah';

    protected $fillable = [
        'jamaah_id',
        'absensi_jamaah_id',
        'absen_ke',
        'tanggal',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function absen()
    {
        return $this->belongsTo(AbsensiJamaah::class, 'absensi_jamaah_id');
    }

    public function tourleader()
    {
        return $this->belongsTo(TourLeader::class, 'created_by');
    }
}

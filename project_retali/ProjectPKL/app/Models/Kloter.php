<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kloter extends Model
{
    use HasFactory;

    protected $table = 'kloters';

    protected $fillable = ['nama', 'tanggal'];

    protected $appends = ['tanggal_label', 'name'];

    public function tourleaders()
    {
        return $this->hasMany(TourLeader::class, 'kloter_id');
    }

    public function getNameAttribute(): string
    {
        return $this->nama ?? ('Kloter #' . $this->id);
    }

    /**
     * INI KUNCI UTAMA
     * Semua logika tanggal DISINI
     */
    public function getTanggalLabelAttribute(): string
    {
        if (!$this->tanggal) {
            return '-';
        }

        // Normalisasi spasi & strip
        $text = trim($this->tanggal);

        // Kalau format sudah rapi → langsung tampilkan
        // contoh: "10 januari - 25 januari 2026"
        return $text;
    }
}

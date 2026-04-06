<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_jamaah', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jamaah_id')
                ->constrained('jamaahs')
                ->cascadeOnDelete();

            $table->foreignId('absensi_jamaah_id')
                ->constrained('absensi_jamaah')
                ->cascadeOnDelete();

            $table->unsignedInteger('absen_ke')->default(1);

            $table->date('tanggal')->index();

            $table->enum('status', [
                'BELUM_ABSEN',
                'HADIR',
                'TIDAK_HADIR'
            ]);

            $table->text('catatan')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('tour_leaders')
                ->nullOnDelete();

            $table->timestamps();

            // 🔥 INI WAJIB UNTUK PERFORMA
            $table->index(['jamaah_id', 'absensi_jamaah_id']);
            $table->index(['absensi_jamaah_id', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_jamaah');
    }
};

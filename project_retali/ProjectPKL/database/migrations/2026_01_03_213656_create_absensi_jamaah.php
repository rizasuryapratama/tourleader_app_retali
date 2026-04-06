<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('absensi_jamaah', function (Blueprint $table) {
            $table->id();

            /**
             * RELASI UTAMA
             * Absen ini MILIK satu kloter
             */
            $table->foreignId('kloter_id')
                ->constrained('kloters')
                ->cascadeOnDelete();

            /**
             * Judul hanya sebagai cache / display (opsional)
             * Sumber kebenaran tetap dari kloter
             */
            $table->string('judul_absen')->nullable();

            /**
             * Konteks sesi
             */
            $table->foreignId('sesi_absen_id')
                ->constrained('sesi_absens')
                ->cascadeOnDelete();

            $table->foreignId('sesi_absen_item_id')
                ->constrained('sesi_absen_items')
                ->cascadeOnDelete();

            $table->timestamps();

            /**
             * 🔒 ANTI DUPLICATE
             * 1 kloter hanya boleh punya 1 sesi konteks
             */
            $table->unique([
                'kloter_id',
                'sesi_absen_id',
                'sesi_absen_item_id'
            ], 'absen_unique_context');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_jamaah');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passport_scans', function (Blueprint $table) {
            $table->id();

            // =============================
            // IDENTIFIER (BUKAN UNIQUE)
            // =============================
            $table->string('passport_number')->index();

            // =============================
            // SNAPSHOT DATA (MANDIRI)
            // =============================
            $table->string('owner_name')->nullable();
            $table->string('owner_phone', 30)->nullable();

            // sementara string dulu (bisa di-relasikan belakangan)
            $table->string('kloter')->nullable();

            // =============================
            // CONTEXT SCAN
            // =============================
            $table->enum('scan_type', [
                'checkin',   // sebelum berangkat
                'boarding',  // naik pesawat / bus
                'arrival',   // tiba
            ])->default('checkin')->index();

            // =============================
            // AUDIT
            // =============================
            $table->foreignId('tourleader_id')
                ->constrained('tour_leaders')
                ->cascadeOnDelete();

            $table->timestamp('scanned_at')->useCurrent()->index();
            $table->timestamps();

            // =============================
            // PROTECTION INDEX
            // =============================
            $table->index([
                'passport_number',
                'scan_type',
                'scanned_at'
            ], 'passport_scan_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_scans');
    }
};

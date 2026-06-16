<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('day_number');
            $table->string('city')->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('item_count')->default(0)
                  ->comment('Jumlah kegiatan yang direncanakan per hari');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_days');
    }
};
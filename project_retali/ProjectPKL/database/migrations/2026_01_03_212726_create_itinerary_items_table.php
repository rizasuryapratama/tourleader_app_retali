<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_day_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence')->default(1);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_items');
    }
};
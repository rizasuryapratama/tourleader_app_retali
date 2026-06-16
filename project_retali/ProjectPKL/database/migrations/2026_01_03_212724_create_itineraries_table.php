<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('send_to')->default('all_tourleaders')
                  ->comment('all_users | all_tourleaders | all_muthawif | selected');
            $table->string('status')->default('draft')
                  ->comment('draft | sent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};
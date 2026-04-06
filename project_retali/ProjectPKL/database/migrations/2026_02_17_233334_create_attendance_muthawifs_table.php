<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_muthawifs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('muthawif_id')
                ->constrained('muthawifs')
                ->onDelete('cascade');

            $table->string('foto')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_muthawifs');
    }
};

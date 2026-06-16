<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_leaders', function (Blueprint $table) {
            $table->string('session_token')->nullable();
        });

        Schema::table('muthawifs', function (Blueprint $table) {
            $table->string('session_token')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tour_leaders', function (Blueprint $table) {
            $table->dropColumn('session_token');
        });

        Schema::table('muthawifs', function (Blueprint $table) {
            $table->dropColumn('session_token');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('task_user', function (Illuminate\Database\Schema\Blueprint $t) {
        $t->id();

       
        $t->foreignIdFor(\App\Models\Task::class) 
          ->constrained()                         
          ->cascadeOnDelete();

       
        $t->unsignedBigInteger('tourleader_id');
        $t->foreign('tourleader_id')
          ->references('id')
          ->on('tour_leaders')                    
          ->cascadeOnDelete();

        $t->timestamp('done_at')->nullable();
        $t->timestamps();

        $t->unique(['task_id','tourleader_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};

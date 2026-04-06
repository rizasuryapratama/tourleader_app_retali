<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_user_answers', function (Blueprint $table) {
            $table->id();

            // Relasi ke task
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            // Relasi ke tour leader
            $table->foreignId('tourleader_id')
                ->constrained('tour_leaders')
                ->cascadeOnDelete();

            // Relasi ke soal
            $table->foreignId('question_id')
                ->constrained('task_questions')
                ->cascadeOnDelete();

            $table->timestamps();

            // ❗ Wajib: satu TL hanya 1 jawaban per soal per task
            $table->unique([
                'task_id',
                'tourleader_id',
                'question_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user_answers');
    }
};

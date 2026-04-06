<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TourLeader;
use Illuminate\Support\Facades\DB;

class TaskResultDetailController extends Controller
{
    public function show(Task $task, TourLeader $tourleader)
    {
        // Ambil semua soal
        $questions = $task->questions()
            ->orderBy('order_no')
            ->get();

        // Ambil soal yang sudah dijawab TL ini
        $answeredIds = DB::table('task_answers')
            ->where('task_id', $task->id)
            ->where('tourleader_id', $tourleader->id)
            ->pluck('task_question_id')
            ->toArray();

        return view('admin.tugas_tourleader.result-detail', compact(
            'task',
            'tourleader',
            'questions',
            'answeredIds'
        ));
    }
}

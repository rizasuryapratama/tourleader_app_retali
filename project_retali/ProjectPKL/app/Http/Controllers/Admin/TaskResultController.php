<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TourLeader;
use Illuminate\Support\Facades\DB;

class TaskResultController extends Controller
{
    // ===============================
    // REKAP: sudah / belum
    // ===============================
    public function show(Task $task)
    {
        $task->load(['doneTourleaders', 'notDoneTourleaders']);

        $done    = $task->doneTourleaders()->get();
        $notDone = $task->notDoneTourleaders()->get();

        return view('admin.tugas_tourleader.result', compact(
            'task',
            'done',
            'notDone'
        ));
    }

    // ===============================
    // DETAIL HASIL PER TOUR LEADER
    // ===============================
    public function detail(Task $task, TourLeader $tourleader)
{
    // Pastikan TL memang di-assign ke task
    $assigned = DB::table('task_user')
        ->where('task_id', $task->id)
        ->where('tourleader_id', $tourleader->id)
        ->first();

    abort_if(!$assigned, 404, 'Tour Leader tidak terdaftar pada tugas ini');

    // Ambil semua soal
    $questions = $task->questions()
        ->orderBy('order_no')
        ->get();

    // ✅ AMBIL ID SOAL YANG SUDAH DIJAWAB
    $answeredIds = DB::table('task_user_answers')
        ->where('task_id', $task->id)
        ->where('tourleader_id', $tourleader->id)
        ->pluck('question_id')
        ->toArray();

    return view('admin.tugas_tourleader.result-detail', [
        'task'        => $task,
        'tourleader'  => $tourleader,
        'questions'   => $questions,
        'answeredIds' => $answeredIds, // 🔑 INI YANG TADI HILANG
        'doneAt'      => $assigned->done_at,
    ]);
}

}

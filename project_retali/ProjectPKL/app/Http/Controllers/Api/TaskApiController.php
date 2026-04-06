<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TaskApiController extends Controller
{
    /**
     * GET /tourleader/tasks
     */
    public function index(Request $request)
    {
        $tl  = $request->user('tourleader');
        $now = now();

        $tasks = $tl->tasks()
            ->withPivot('done_at')
            ->orderByDesc('opens_at')
            ->get();

        $data = $tasks->map(function ($t) use ($now) {
            $status = $now->lt($t->opens_at)
                ? 'belum_dibuka'
                : ($now->gt($t->closes_at) ? 'ditutup' : 'dibuka');

            $doneAt = $t->pivot && $t->pivot->done_at
                ? Carbon::parse($t->pivot->done_at)->toIso8601String()
                : null;

            return [
                'id'             => $t->id,
                'title'          => $t->title,
                'question_count' => $t->question_count,
                'opens_at'       => $t->opens_at->toIso8601String(),
                'closes_at'      => $t->closes_at->toIso8601String(),
                'status'         => $status,
                'done_at'        => $doneAt,
                'can_work'       => $status === 'dibuka' && is_null($doneAt),
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * GET /tourleader/tasks/{task}
     */
    public function show(Request $request, Task $task)
    {
        $tl = $request->user('tourleader');

        $assigned = DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('tourleader_id', $tl->id)
            ->first();

        if (!$assigned) {
            return response()->json([
                'message' => 'Tugas tidak ditemukan untuk akun ini'
            ], 404);
        }

        $task->load(['questions' => fn ($q) => $q->orderBy('order_no')]);

        $now    = now();
        $status = $now->lt($task->opens_at)
            ? 'belum_dibuka'
            : ($now->gt($task->closes_at) ? 'ditutup' : 'dibuka');

        return response()->json([
            'id'             => $task->id,
            'title'          => $task->title,
            'question_count' => $task->question_count,
            'opens_at'       => $task->opens_at->toIso8601String(),
            'closes_at'      => $task->closes_at->toIso8601String(),
            'status'         => $status,
            'done_at'        => $assigned->done_at
                ? Carbon::parse($assigned->done_at)->toIso8601String()
                : null,
            'questions'      => $task->questions->map(fn ($q) => [
                'id'            => $q->id,
                'order_no'      => $q->order_no,
                'question_text' => $q->question_text,
            ]),
        ]);
    }

    /**
     * GET /tourleader/tasks/{task}/answers
     */
    public function answers(Request $request, Task $task)
    {
        $tl = $request->user('tourleader');

        $assigned = DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('tourleader_id', $tl->id)
            ->exists();

        if (!$assigned) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        $answeredIds = DB::table('task_user_answers')
            ->where('task_id', $task->id)
            ->where('tourleader_id', $tl->id)
            ->pluck('question_id')
            ->toArray();

        return response()->json([
            'answers' => $answeredIds
        ]);
    }

    /**
     * POST /tourleader/tasks/{task}/questions/{question}/answer
     */
    public function answer(Request $request, Task $task, $questionId)
    {
        $tl = $request->user('tourleader');

        DB::table('task_user_answers')->updateOrInsert(
            [
                'task_id'       => $task->id,
                'tourleader_id' => $tl->id,
                'question_id'   => $questionId,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /tourleader/tasks/{task}/questions/{question}/answer
     */
    public function unanswer(Request $request, Task $task, $questionId)
    {
        $tl = $request->user('tourleader');

        DB::table('task_user_answers')
            ->where('task_id', $task->id)
            ->where('question_id', $questionId)
            ->where('tourleader_id', $tl->id)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * POST /tourleader/tasks/{task}/done
     */
    public function markDone(Request $request, Task $task)
    {
        $tl = $request->user('tourleader');

        $pivot = DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('tourleader_id', $tl->id);

        $row = $pivot->first();
        if (!$row) {
            return response()->json([
                'message' => 'Tugas tidak ditemukan untuk akun ini'
            ], 404);
        }

        $now = now();

        if ($now->lt($task->opens_at)) {
            return response()->json(['message' => 'Tugas belum dibuka'], 422);
        }
        if ($now->gt($task->closes_at)) {
            return response()->json(['message' => 'Tugas sudah ditutup'], 422);
        }

        if (!is_null($row->done_at)) {
            return response()->json(['message' => 'Tugas sudah diselesaikan'], 200);
        }

        $totalSoal = $task->questions()->count();

        $answered = DB::table('task_user_answers')
            ->where('task_id', $task->id)
            ->where('tourleader_id', $tl->id)
            ->count();

        if ($answered < $totalSoal) {
            return response()->json([
                'message' => 'Masih ada tugas yang belum diselesaikan'
            ], 422);
        }

        $pivot->update(['done_at' => $now]);

        return response()->json([
            'success' => true,
            'done_at' => $now->toIso8601String(),
        ]);
    }
}

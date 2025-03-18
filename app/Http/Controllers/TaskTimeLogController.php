<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskTimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTimeLogController extends Controller
{
    /**
     * Log time spent on a task
     */
    public function store(Request $request, Task $task)
    {
        // Only the assigned user can log time.
        if ($task->assigned_to !== Auth::id()) {
            return response()->json(['error' => 'Only the assigned user can log time for this task'], 403);
        }

        $validated = $request->validate([
            'minutes' => 'required|integer|min:1',
        ]);

        $timeLog = TaskTimeLog::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'minutes' => $validated['minutes'],
        ]);

        // Update the total_time_spent on the task table
        $task->increment('total_time_spent', $validated['minutes']);

        return response()->json($timeLog, 201);
    }

    /**
     * Get all time logs for a task
     */
    public function index(Task $task)
    {
        // Retrieve all time logs for the task
        $timeLogs = $task->timeLogs()->with('user')->get();

        return response()->json($timeLogs);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Support\Facades\Auth;

class TaskFileController extends Controller
{
    public function upload(Request $request, Task $task)
    {
        // Only the creator or assigned user can upload files
        if ($task->created_by !== Auth::id() && $task->assigned_to !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized', 'auth' => Auth::id(), 'created_by' => var_dump($task)], 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->store("tasks/{$task->id}", 'public');

        $taskFile = TaskFile::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'file_path' => str_replace('public/', '', $filePath),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file' => $taskFile,
        ], 201);
    }
}

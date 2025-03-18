<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * List all tasks
     */
    public function index()
    {
        $tasks = Task::with(['createdBy', 'assignedTo'])->get();

        return response()->json($tasks);
    }

    /**
     * Create a new task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed'])],
            'due_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['status'] = $validated['status'] ?? 'pending';

        $validated['created_by'] = Auth::id();

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    /**
     * Show a specific task
     */
    public function show(Task $task)
    {
        $task->load(['comments', 'timeLogs', 'files']);

        return response()->json($task);
    }

    /**
     * Update a task
     */
    public function update(Request $request, Task $task)
    {
        // Only the creator or assigned user can update a task
        if ($task->created_by !== Auth::id() && $task->assigned_to !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed'])],
            'due_date' => 'sometimes|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Status can only be changed to completed by the assigned user
        if (isset($validated['status']) && $validated['status'] === 'completed') {
            if ($task->assigned_to !== Auth::id()) {
                return response()->json(['error' => 'Status can only be changed to completed by the assigned user'], 403);
            }
        }

        if (! isset($validated['status'])) {
            unset($validated['status']);
        }

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Delete a task
     */
    public function destroy(Task $task)
    {
        // Only the creator can delete a task
        if ($task->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}

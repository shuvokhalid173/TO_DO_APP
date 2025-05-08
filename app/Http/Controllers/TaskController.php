<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->tasks()->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    public function markAsCompleted(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->update(['is_completed' => true]);

        return response()->json(['message' => 'Task marked as completed']);
    }

    protected function authorizeTask(Request $request, Task $task)
    {
        if ($request->user()->id !== $task->user_id) {
            abort(403, 'Unauthorized');
        }
    }
}

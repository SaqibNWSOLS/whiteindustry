<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:not_started,in_progress,completed',
            'related' => 'nullable|string',
        ]);

        // Try to accept assigned user id if numeric, otherwise leave null
        $assigned = null;
        if (!empty($data['assigned_to']) && is_numeric($data['assigned_to'])) {
            $assigned = (int) $data['assigned_to'];
        }

        // Normalize due_date to Y-m-d or null
        $due = null;
        if (!empty($data['due_date'])) {
            try {
                $due = Carbon::parse($data['due_date'])->toDateString();
            } catch (\Exception $e) {
                $due = null;
            }
        }

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'assigned_to' => $assigned,
            'due_date' => $due,
            'priority' => $data['priority'] ?? 'normal',
            'status' => $data['status'] ?? 'not_started',
            // related is a human label for now; storing in related_type/related_id requires structured input
        ]);

        // Load assigned user relation for frontend convenience
        $task->load('assignedUser');

        // If the request expects JSON (AJAX/API), return JSON. Otherwise redirect back to the workflow
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created',
                'data' => $task,
            ], 201);
        }

        // For a normal form submit, redirect back to the workflow page with a flash message
        return redirect('/workflow')->with('success', 'Task created successfully');
    }

    /**
     * Return task details as JSON
     */
    public function show(Task $task)
    {
        $task->load('assignedUser');
        // If request expects JSON, return JSON (used by AJAX callers).
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json(["success" => true, 'data' => $task]);
        }

        // If this is an AJAX request (modal), return a partial fragment without the full layout
        if (request()->ajax()) {
            return view('tasks.partials.show_fragment', compact('task'));
        }

        return view('tasks.show', compact('task'));
    }

    /**
     * Show edit form as an HTML fragment (for modal use) or full page.
     */
    public function edit(Task $task)
    {
        $task->load('assignedUser');
        // If AJAX, return a compact edit fragment (frontend will usually use openEditTask to populate modal)
        if (request()->ajax()) {
            return view('tasks.edit', compact('task'));
        }
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update existing task
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:not_started,in_progress,completed',
            'related' => 'nullable|string',
        ]);

        $assigned = null;
        if (!empty($data['assigned_to']) && is_numeric($data['assigned_to'])) {
            $assigned = (int) $data['assigned_to'];
        }

        // Normalize due_date
        $due = null;
        if (!empty($data['due_date'])) {
            try {
                $due = Carbon::parse($data['due_date'])->toDateString();
            } catch (\Exception $e) {
                $due = null;
            }
        }

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'assigned_to' => $assigned,
            'due_date' => $due,
            'priority' => $data['priority'] ?? $task->priority,
            'status' => $data['status'] ?? $task->status,
        ]);

        $task->load('assignedUser');

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task updated', 'data' => $task]);
        }

        return redirect('/workflow')->with('success', 'Task updated successfully');
    }

    /**
     * Delete a task (soft delete)
     */
    public function destroy(Task $task)
    {
        $task->delete();
        // Support both AJAX and non-AJAX deletes
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task deleted']);
        }

        return redirect('/workflow')->with('success', 'Task deleted');
    }
}

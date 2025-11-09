<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['assignedUser']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:not_started,in_progress,completed',
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
        ]);

        $task = Task::create($validated);
        return response()->json($task->load(['assignedUser']), 201);
    }

    public function show(Task $task)
    {
        return response()->json($task->load(['assignedUser']));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:not_started,in_progress,completed',
        ]);

        $task->update($validated);
        return response()->json($task->load(['assignedUser']));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function markComplete(Task $task)
    {
        $task->update(['status' => 'completed']);
        return response()->json($task);
    }

    /**
     * Return tasks as calendar events.
     * FullCalendar typically sends `start` and `end` query params; use them to limit results.
     */
    public function events(Request $request)
    {
        $query = Task::with(['assignedUser']);

        // Filter by date range if provided (expecting YYYY-MM-DD or ISO dates)
        if ($request->has('start') && $request->has('end')) {
            try {
                $start = $request->get('start');
                $end = $request->get('end');
                $query->whereBetween('due_date', [$start, $end]);
            } catch (\Exception $e) {
                // ignore parsing errors and return all
            }
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->get('assigned_to'));
        }

        $tasks = $query->get();

        $events = $tasks->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'start' => $t->due_date ? $t->due_date->toDateString() : null,
                'allDay' => true,
                'url' => url(route('tasks.show', $t->id)),
                'extendedProps' => [
                    'priority' => $t->priority,
                    'status' => $t->status,
                    'assigned_to' => $t->assigned_to,
                ],
                'color' => $t->priority === 'high' ? '#ef4444' : ($t->priority === 'low' ? '#3b82f6' : '#f59e0b'),
            ];
        });

        return response()->json($events->values());
    }
}

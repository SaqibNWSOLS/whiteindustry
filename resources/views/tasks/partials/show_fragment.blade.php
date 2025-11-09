<div class="p-4">
    <h4>Task {{ $task->task_id ?? ('TSK-' . $task->id) }}</h4>
    <div class="mb-2"><strong>Title:</strong> {{ $task->title }}</div>
    <div class="mb-2"><strong>Description:</strong> {{ $task->description }}</div>
    <div class="mb-2"><strong>Assigned To:</strong> {{ optional($task->assignedUser)->full_name ?? $task->assigned_to }}</div>
    <div class="mb-2"><strong>Due Date:</strong> {{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}</div>
    <div class="mb-2"><strong>Priority:</strong> {{ $task->priority }}</div>
    <div class="mb-2"><strong>Status:</strong> {{ $task->status }}</div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
        <button class="btn btn-secondary" onclick="hideModal()">Close</button>
        <button class="btn btn-primary" onclick="openEditTask({{ $task->id }})">Edit</button>
    </div>
</div>
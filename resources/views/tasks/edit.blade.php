@extends('layouts.app')


@section('content')

<div class="content">
    <form id="task-edit-form" method="POST" action="{{ route('tasks.update', $task->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group"><label class="form-label">Task ID</label><input type="text" class="form-input" value="{{ $task->task_id ?? ('TSK-' . $task->id) }}" readonly></div>
        <div class="form-group"><label class="form-label">Title</label><input type="text" name="title" class="form-input" value="{{ $task->title }}" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-input" rows="3">{{ $task->description }}</textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            @php
                $assignableUsers = App\Models\User::whereDoesntHave('roles', function($q){ $q->where('name','customer'); })->orderBy('first_name')->take(200)->get();
            @endphp
            <div class="form-group"><label class="form-label">Assigned To</label>
                <select name="assigned_to" class="form-select">
                    <option value="">Select user</option>
                    @foreach($assignableUsers as $u)
                        <option value="{{ $u->id }}" {{ $task->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-input" value="{{ $task->due_date? $task->due_date->format('Y-m-d') : '' }}"></div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
            <a type="button" class="btn btn-secondary" href="/workflow">Cancel</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
@endsection
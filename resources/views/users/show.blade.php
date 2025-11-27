@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">User Details</h1>
            <div>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('users.index') }}" class="btn">Back to List</a>
            </div>
        </div>

        <div class="card">
            <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                <div>
                    <label class="form-label">First name</label>
                    <div class="form-static">{{ $user->first_name }}</div>
                </div>
                <div>
                    <label class="form-label">Last name</label>
                    <div class="form-static">{{ $user->last_name }}</div>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <div class="form-static">{{ $user->email }}</div>
                </div>
                <div>
                    <label class="form-label">Role</label>
                    <div class="form-static">{{ $user->roles->pluck('display_name')->join(', ') }}</div>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <div class="form-static">
                        <span class="status-badge status-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">Phone</label>
                    <div class="form-static">{{ $user->phone ?? 'N/A' }}</div>
                </div>
                <div>
                    <label class="form-label">Job title</label>
                    <div class="form-static">{{ $user->job_title ?? 'N/A' }}</div>
                </div>
                <div>
                    <label class="form-label">Department</label>
                    <div class="form-static">{{ $user->department ?? 'N/A' }}</div>
                </div>
                <div>
                    <label class="form-label">Created At</label>
                    <div class="form-static">{{ $user->created_at->format('M j, Y g:i A') }}</div>
                </div>
                <div>
                    <label class="form-label">Updated At</label>
                    <div class="form-static">{{ $user->updated_at->format('M j, Y g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
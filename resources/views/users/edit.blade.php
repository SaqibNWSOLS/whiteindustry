@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">Edit User</h1>
        </div>

        <div class="card">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div>
                        <label class="form-label">First name</label>
                        <input name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-input" required />
                        @error('first_name')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Last name</label>
                        <input name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-input" required />
                        @error('last_name')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" class="form-input" required />
                        @error('email')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input name="password" type="password" class="form-input" placeholder="Leave blank to keep current" />
                        @error('password')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select">
                            <option value="">-- Select role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (old('role_id') ?? $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Job title</label>
                        <input name="job_title" value="{{ old('job_title', $user->job_title) }}" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Department</label>
                        <input name="department" value="{{ old('department', $user->department) }}" class="form-input" />
                    </div>
                    <div style="grid-column: 1 / -1; display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('users.index') }}" class="btn">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
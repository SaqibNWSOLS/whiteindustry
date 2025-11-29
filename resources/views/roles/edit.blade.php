@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">Edit Role: {{ $role->display_name }}</h1>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back to Roles</a>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ $role->name }}" readonly>
                    <small class="text-muted">Role name cannot be changed</small>
                </div>

               
                <div class="form-group">
                    <label>Permissions</label>
                    <div class="permissions-grid">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="permission-group">
                                <h4 class="permission-group-title">{{ $group ?: 'General' }}</h4>
                                <div class="permission-list">
                                    @foreach($groupPermissions as $permission)
                                        <label class="permission-item">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                            <span>{{ $permission->display_name ?? $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Role</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }
        .permission-group {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .permission-group-title {
            font-weight: 600;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .permission-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .permission-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
        }
        .permission-item input[type="checkbox"] {
            margin: 0;
        }
    </style>
@endsection
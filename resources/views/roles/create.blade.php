@extends('layouts.app')

@section('title', __('roles.create_role'))
@section('page_title', __('roles.create_role'))
@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">{{ __('roles.create_role') }}</h1>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('roles.back_to_roles') }}</a>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">{{ __('roles.role_name') }} *</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                
                <div class="form-group">
                    <label>{{ __('roles.permissions') }}</label>
                    <div class="permissions-grid">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="permission-group">
                                <h4 class="permission-group-title">{{ $group ?: __('roles.general') }}</h4>
                                <div class="permission-list">
                                    @foreach($groupPermissions as $permission)
                                        <label class="permission-item">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                            <span>{{ $permission->display_name ?? $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ __('roles.create_role_button') }}</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('roles.cancel') }}</a>
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
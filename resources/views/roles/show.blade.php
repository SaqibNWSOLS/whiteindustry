@extends('layouts.app')

@section('title', __('roles.show_role'))
@section('page_title', __('roles.show_role'))
@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">{{ __('roles.show_role') }}: {{ $role->display_name ?? $role->name }}</h1>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('roles.back_to_roles') }}</a>
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">{{ __('roles.edit_role_button') }}</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="card">
                    <h3 class="card-title">{{ __('roles.role_information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="detail-label">{{ __('roles.system_name') }}</label>
                            <p class="detail-value">{{ $role->name }}</p>
                        </div>
                        <div>
                            <label class="detail-label">{{ __('roles.users_count') }}</label>
                            <p class="detail-value">{{ __('roles.users_count_label', ['count' => $usersCount]) }}</p>
                        </div>
                        <div>
                            <label class="detail-label">{{ __('roles.permissions_count') }}</label>
                            <p class="detail-value">{{ __('roles.permissions_count_label', ['count' => $rolePermissions->count()]) }}</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-6">
                    <h3 class="card-title">{{ __('roles.assigned_permissions') }}</h3>
                    
                    @if($rolePermissions->count() > 0)
                        <div class="permissions-grid">
                            @foreach($rolePermissions->groupBy('group') as $group => $permissions)
                                <div class="permission-group">
                                    <h4 class="permission-group-title">{{ $group ?: __('roles.general') }}</h4>
                                    <div class="permission-list">
                                        @foreach($permissions as $permission)
                                            <div class="permission-item">
                                                <span>{{ $permission->display_name ?? $permission->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('roles.no_permissions_assigned') }}</p>
                    @endif
                </div>
            </div>

            <div>
                <div class="card">
                    <h3 class="card-title">{{ __('roles.quick_actions') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary w-full">{{ __('roles.edit_role_button') }}</a>
                        
                        @if($usersCount === 0)
                            <form action="{{ route('roles.destroy', $role) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-full" onclick="return confirm('{{ __('roles.delete_confirmation') }}')">
                                    {{ __('roles.delete_role') }}
                                </button>
                            </form>
                        @else
                            <button class="btn btn-danger w-full" disabled title="{{ __('roles.cannot_delete_role_with_users') }}">
                                {{ __('roles.delete_role') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .permission-group {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }
        .permission-group-title {
            font-weight: 600;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .permission-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .permission-item {
            padding: 4px 0;
            font-size: 14px;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }
        .detail-value {
            margin-top: 4px;
            font-size: 16px;
        }
    </style>
@endsection
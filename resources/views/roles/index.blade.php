@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">Roles Management</h1>
            <div>
                <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>All Roles</h3>
                <div>
                    <form method="GET" action="{{ route('roles.index') }}">
                        <input name="search" value="{{ request('search') }}" class="form-input" placeholder="Search roles..." style="width:220px;" />
                    </form>
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Permissions Count</th>
                        <th style="width:160px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">Edit</a>
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No roles found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
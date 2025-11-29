@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">Users Management</h1>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-primary">Roles</a>
                <a href="{{ route('users.create') }}" class="btn btn-primary">Create user</a>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>All users</h3>
                <div>
                    <form method="GET" action="{{ route('users.index') }}">
                        <input name="search" value="{{ request('search') }}" class="form-input" placeholder="Search users..." style="width:220px;" />
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
                        <th>Email</th>
                        <th>Name</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th style="width:160px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td>
                                <span class="status-badge status-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
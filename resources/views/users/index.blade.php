@extends('layouts.app')
@section('title', __('users.management'))
@section('page_title', __('users.management'))

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">{{ __('users.management') }}</h1>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-primary">{{ __('users.roles') }}</a>
                <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('users.create_user') }}</a>
            </div>
        </div>

        <div class="table-container">
            
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

            <table id="quotesTable">
                <thead>
                    <tr>
                        <th>{{ __('users.email') }}</th>
                        <th>{{ __('users.name') }}</th>
                        <th>{{ __('users.roles') }}</th>
                        <th>{{ __('users.status') }}</th>
                        <th style="width:160px; text-align:right;">{{ __('users.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td>
                                <span class="status-badge status-{{ $user->status }}">
                                    {{ __("users.{$user->status}") }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-secondary" title="{{ __('users.view') }}">
                                        <i class="fas fa-eye"></i> 
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary" title="{{ __('users.edit') }}">
                                        <i class="fas fa-edit"></i> 
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="{{ __('users.delete') }}" onclick="return confirm('{{ __('users.delete_confirmation') }}')">
                                            <i class="fas fa-trash"></i> 
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('users.no_users_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
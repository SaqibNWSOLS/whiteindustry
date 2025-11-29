@extends('layouts.app')

@section('title', __('roles.management'))
@section('page_title', __('roles.management'))

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">{{ __('roles.management') }}</h1>
            <div>
                <a href="{{ route('roles.create') }}" class="btn btn-primary">{{ __('roles.create_role_button') }}</a>
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
                        <th>{{ __('roles.name') }}</th>
                        <th>{{ __('roles.permissions_count') }}</th>
                        <th style="width:160px; text-align:right;">{{ __('roles.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary" title="{{ __('roles.show_role') }}">
                                        <i class="fas fa-eye"></i> 
                                    </a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary" title="{{ __('roles.edit_role_button') }}">
                                        <i class="fas fa-edit"></i> 
                                    </a>
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="{{ __('roles.delete_role') }}" onclick="return confirm('{{ __('roles.delete_confirmation') }}')">
                                            <i class="fas fa-trash"></i> 
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
            
        </div>
    </div>
@endsection
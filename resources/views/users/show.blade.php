@extends('layouts.app')

@section('title', __('users.user_details'))
@section('page_title', __('users.user_details'))
@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">{{ __('users.user_details') }}</h1>
            <div>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">{{ __('users.edit') }}</a>
                <a href="{{ route('users.index') }}" class="btn">{{ __('users.back_to_list') }}</a>
            </div>
        </div>

        <div class="card">
            <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                <div>
                    <label class="form-label">{{ __('users.first_name') }}</label>
                    <div class="form-static">{{ $user->first_name }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.last_name') }}</label>
                    <div class="form-static">{{ $user->last_name }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.email') }}</label>
                    <div class="form-static">{{ $user->email }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.role') }}</label>
                    <div class="form-static">{{ $user->roles->pluck('display_name')->join(', ') }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.status') }}</label>
                    <div class="form-static">
                        <span class="status-badge status-{{ $user->status }}">
                            {{ __("users.{$user->status}") }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.phone') }}</label>
                    <div class="form-static">{{ $user->phone ?? __('users.not_available') }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.job_title') }}</label>
                    <div class="form-static">{{ $user->job_title ?? __('users.not_available') }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.department') }}</label>
                    <div class="form-static">{{ $user->department ?? __('users.not_available') }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.created_at') }}</label>
                    <div class="form-static">{{ $user->created_at->format('M j, Y g:i A') }}</div>
                </div>
                <div>
                    <label class="form-label">{{ __('users.updated_at') }}</label>
                    <div class="form-static">{{ $user->updated_at->format('M j, Y g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
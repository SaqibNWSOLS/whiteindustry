@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="content">
    <div class="module-header">
        <h1 class="text-2xl font-semibold">Notifications</h1>
        <div class="actions">
            <form method="POST" action="{{ route('notifications.delete-all') }}" class="inline">
                @csrf
                <button type="submit" class="btn">Delete All</button>
            </form>
             <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                @csrf
                <button type="submit" class="btn">Mark all read</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div id="notifications-container">
            @if($notifications->isEmpty())
                <div class="empty-state">No notifications</div>
            @else
                <ul class="notification-list">
                    @foreach($notifications as $notification)
                        <li class="notification {{ $notification->is_read ? '' : 'unread' }}">
                            <div class="notification-content">
                                <div class="meta">
                                    <strong class="notification-title">{{ $notification->title ?? $notification->type ?? 'System' }}</strong>
                                    <span class="notification-time">{{ $notification->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="body">{{ $notification->message }}</div>
                                <div class="actions">
                                    @if($notification->url)
                                        <a href="{{ $notification->url }}" class="btn btn-sm" target="_blank">View</a>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('notifications.toggle-read', $notification->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm">
                                            {{ $notification->is_read ? 'Mark Unread' : 'Mark Read' }}
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('notifications.delete', $notification->id) }}" class="inline" onsubmit="return confirm('Delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                @if($notifications->hasPages())
                    <div class="pagination-wrapper">
                        {{ $notifications->links('pagination::simple-tailwind') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.notification-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notification {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    transition: background-color 0.2s;
}

.notification:hover {
    background-color: #f8fafc;
}

.notification.unread {
    background-color: #f0f9ff;
    border-left: 3px solid #3b82f6;
}

.notification:last-child {
    border-bottom: none;
}

.notification-content {
    width: 100%;
}

.meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}

.notification-title {
    color: #1f2937;
    font-size: 0.875rem;
}

.notification-time {
    color: #6b7280;
    font-size: 0.75rem;
}

.body {
    margin-bottom: 12px;
    color: #4b5563;
    line-height: 1.5;
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.actions form {
    margin: 0;
}

.btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 500;
}

.btn:hover {
    background-color: #2563eb;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 4px 8px;
    font-size: 0.75rem;
}

.btn-danger {
    background-color: #ef4444;
}

.btn-danger:hover {
    background-color: #dc2626;
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
    color: #6b7280;
    font-style: italic;
}

.pagination-wrapper {
    padding: 20px 16px;
    border-top: 1px solid #e5e7eb;
}

/* Custom Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination li {
    display: inline-block;
}

.pagination .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
    min-width: 40px;
}

.pagination .page-link:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}

.pagination .active .page-link {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.pagination .disabled .page-link {
    color: #9ca3af;
    cursor: not-allowed;
    background-color: #f9fafb;
}

.pagination .gap {
    padding: 8px 4px;
    color: #6b7280;
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.module-header h1 {
    color: #1f2937;
    margin: 0;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Responsive Design */
@media (max-width: 640px) {
    .module-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .meta {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .actions {
        flex-direction: column;
        width: 100%;
    }
    
    .actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
    
    .notification {
        padding: 12px;
    }
}
</style>
{{-- 
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('Success: {{ session('success') }}');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('Error: {{ session('error') }}');
    });
</script>
@endif --}}
@endsection
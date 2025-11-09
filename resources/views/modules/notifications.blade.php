@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="content">
    <div class="module-header">
        <h1 class="text-2xl font-semibold">Notifications</h1>
        <div class="actions">
            <button class="btn" onclick="markAllRead()">Mark all read</button>
        </div>
    </div>

    <div class="card">
            <div id="notifications-container">
                <div style="padding:12px;color:#666;">Loading notifications...</div>
            </div>
    </div>
</div>

<script>
    async function loadNotifications(){
        const container = document.getElementById('notifications-container');
        if (!container) return;
        container.innerHTML = '<div style="padding:12px;color:#666;">Loading notifications...</div>';
        try {
            const res = await fetch('/api/notifications', { credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to load');
            const payload = await res.json();
            const items = payload.data || payload.data === undefined ? (payload.data || payload) : payload;
            // If paginated, payload.data is array
            const list = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : (payload.data || []));
            if (!list || list.length === 0) {
                container.innerHTML = '<div style="padding:12px;color:#666;">No notifications</div>';
                return;
            }
            const ul = document.createElement('ul');
            ul.className = 'notification-list';
            for (const n of list) {
                const li = document.createElement('li');
                li.className = 'notification' + (n.is_read ? '' : ' unread');
                const meta = document.createElement('div'); meta.className = 'meta'; meta.innerHTML = `<strong>${escapeHtml(n.title||n.type||'System')}</strong> • ${n.created_at ? (new Date(n.created_at)).toLocaleString() : ''}`;
                const body = document.createElement('div'); body.className = 'body'; body.textContent = n.message || '';
                const actions = document.createElement('div'); actions.className = 'actions';
                const viewBtn = document.createElement('button'); viewBtn.className = 'btn btn-sm'; viewBtn.textContent = 'View';
                viewBtn.onclick = () => { if (n.url) loadIntoModal(n.url, n.title||'Notification'); else showToaster('info', n.title||'Notification', n.message||''); };
                const markBtn = document.createElement('button'); markBtn.className = 'btn btn-sm'; markBtn.style.marginLeft='8px'; markBtn.textContent = n.is_read ? 'Mark Unread' : 'Mark Read';
                markBtn.onclick = () => toggleRead(n.id, !n.is_read);
                const delBtn = document.createElement('button'); delBtn.className = 'btn btn-sm btn-danger'; delBtn.style.marginLeft='8px'; delBtn.textContent = 'Delete';
                delBtn.onclick = () => deleteNotification(n.id);
                // actions.appendChild(viewBtn);
                actions.appendChild(markBtn); actions.appendChild(delBtn);
                li.appendChild(meta); li.appendChild(body); li.appendChild(actions);
                ul.appendChild(li);
            }
            container.innerHTML = '';
            container.appendChild(ul);
        } catch (e) {
            console.error('loadNotifications error', e);
            container.innerHTML = '<div style="padding:12px;color:#b00;">Failed to load notifications</div>';
        }
    }

    async function markAllRead(){
        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const headers = { 'X-Requested-With':'XMLHttpRequest' };
            if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
            const res = await fetch('/api/notifications/mark-all-read', { method: 'POST', credentials:'same-origin', headers });
            if (!res.ok) throw new Error('Failed');
            showToaster('success','Notifications','All notifications marked read');
            loadNotifications();
        } catch (e) {
            console.error('markAllRead error', e);
            showToaster('error','Error','Failed to mark all as read');
        }
    }

    async function toggleRead(id, read){
        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const headers = { 'X-Requested-With':'XMLHttpRequest' };
            if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
            const url = `/api/notifications/${id}/mark-as-read`;
            const res = await fetch(url, { method: 'POST', credentials:'same-origin', headers });
            if (!res.ok) throw new Error('Failed');
            loadNotifications();
        } catch (e) {
            console.error('toggleRead error', e);
            showToaster('error','Error','Failed to update notification');
        }
    }

    async function deleteNotification(id){
        if (!confirm('Delete this notification?')) return;
        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const headers = { 'X-Requested-With':'XMLHttpRequest' };
            if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
            const res = await fetch(`/api/notifications/${id}`, { method: 'DELETE', credentials:'same-origin', headers });
            if (!res.ok) throw new Error('Failed');
            showToaster('success','Deleted','Notification deleted');
            loadNotifications();
        } catch (e) {
            console.error('deleteNotification error', e);
            showToaster('error','Error','Failed to delete notification');
        }
    }

    // escapeHtml helper (simple)
    function escapeHtml(str){ if (!str) return ''; return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

    document.addEventListener('DOMContentLoaded', function(){ loadNotifications(); });
</script>

@endsection

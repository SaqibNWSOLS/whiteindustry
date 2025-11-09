<header class="header">
    <div class="header-content">
        <h1 id="page-title">@yield('page_title', 'Dashboard')</h1>
        <div class="header-actions">
            @auth
                <button id="sidebar-toggle" class="btn btn-secondary" title="Toggle sidebar" aria-pressed="false">☰</button>
                <button id="header-quick-add-btn" class="btn btn-secondary" onclick="(window.showQuickAdd||showQuickAdd)()">Quick Add</button>
                <button id="header-new-order-btn" class="btn btn-primary" onclick="(window.createItem||createItem)('order')">New Order</button>
                <button id="header-notifications-btn" style="position: relative; background: none; border: none; cursor: pointer; padding: 8px 12px; display: flex; align-items: center; justify-content: center;" onclick="window.location.href='{{ url('/notifications') }}'" title="Notifications" aria-label="Notifications">
                    <i class="uil uil-bell"></i>
                    <span id="notifications-badge" aria-hidden="true" style="display:none; position:absolute; top:6px; right:6px; min-width:18px; height:18px; padding:0 5px; background:#dc2626; color:#fff; font-size:12px; border-radius:9px; display:flex; align-items:center; justify-content:center;"></span>
                </button>
                <form id="logoutForm" method="POST" action="{{ url('/logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            @else
                <a href="{{ url('/login') }}" class="btn btn-primary">Login</a>
            @endauth
        </div>
    </div>
</header>
<script>
    (function(){
        async function fetchUnreadCount(){
            try{
                const res = await fetch('/api/notifications?unread=1&per_page=1', { credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} });
                if (!res.ok) return 0;
                const payload = await res.json();
                // Laravel paginator returns meta.total
                let count = 0;
                if (payload && payload.meta && typeof payload.meta.total !== 'undefined') count = payload.meta.total;
                else if (typeof payload.total !== 'undefined') count = payload.total;
                else if (Array.isArray(payload.data)) count = payload.data.length;
                else if (Array.isArray(payload)) count = payload.length;
                return Number(count) || 0;
            }catch(e){ console.error('fetchUnreadCount', e); return 0; }
        }

        async function updateBadge(){
            const badge = document.getElementById('notifications-badge');
            if (!badge) return;
            const count = await fetchUnreadCount();
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : String(count);
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Initial load and polling
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function(){ updateBadge(); setInterval(updateBadge, 30000); });
        else { updateBadge(); setInterval(updateBadge, 30000); }
    })();
</script>
<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('logo.png') }}" alt="logo">
    </div>

    <nav class="nav-menu">
        {{-- Top-level primary modules (kept in same order as original index.html) --}}
        <div class="nav-item {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" data-module="dashboard">
            <a href="{{ url('/dashboard') }}" class="nav-button">
                <i class="uil uil-estate nav-icon"></i>
                <span class="nav-label">Dashboard</span>
            </a>
        </div>

        <div class="nav-item {{ request()->is('crm*') ? 'active' : '' }}" data-module="crm">
            <a href="{{ url('/crm') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-users-alt nav-icon"></i>
                <span class="nav-label">CRM</span>
            </a>
        </div>

        <div class="nav-item {{ request()->is('production*') ? 'active' : '' }}" data-module="production">
            <a href="{{ url('/production') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-cube nav-icon"></i>
                <span class="nav-label">Production</span>
            </a>
        </div>

        <div class="nav-item {{ request()->is('inventory*') ? 'active' : '' }}" data-module="inventory">
            <a href="{{ url('/inventory') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-box nav-icon"></i>
                <span class="nav-label">Inventory</span>
            </a>
        </div>

        <div class="nav-item {{ request()->is('products*') ? 'active' : '' }}" data-module="products">
            <a href="{{ url('/products') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-shopping-bag nav-icon"></i>
                <span class="nav-label">Products</span>
            </a>
        </div>

        <div class="nav-item {{ request()->is('orders*') ? 'active' : '' }}" data-module="orders">
            <a href="{{ url('/orders') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-receipt nav-icon"></i>
                <span class="nav-label">Orders</span>
                {{-- optional dynamic badge for pending invoices --}}
                {{-- <span style="margin-left:auto; background:#f59e0b; color:#fff; border-radius:12px; padding:2px 8px; font-size:11px; font-weight:700;" id="nav-orders-pending">{{ $navCounts['orders_pending'] ?? '' }}</span> --}}
            </a>
        </div>

        @auth
            @if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('manager'))
                <div class="nav-item" data-module="users">
                    <a href="{{ route_if_exists('users.index') ?: url('/users') }}" class="nav-button">
                        <i class="uil uil-user nav-icon"></i>
                        <span class="nav-label">Users</span>
                    </a>
                </div>
            @endif
        @endauth

        <div class="nav-item {{ request()->is('invoicing*') ? 'active' : '' }}" data-module="invoicing">
            <a href="{{ url('/invoicing') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-invoice nav-icon"></i>
                <span class="nav-label">Invoicing</span>
            </a>
        </div>

        {{-- <div class="nav-item {{ request()->is('reports*') ? 'active' : '' }}" data-module="reports">
            <a href="{{ url('/reports') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-chart-line nav-icon"></i>
                <span class="nav-label">Reports</span>
            </a>
        </div> --}}

        <div class="nav-item {{ request()->is('workflow*') ? 'active' : '' }}" data-module="workflow">
            <a href="{{ url('/workflow') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-sitemap nav-icon"></i>
                <span class="nav-label">Workflow</span>
                {{-- small badge for tasks/notifications if needed --}}
            </a>
        </div>

        <div class="nav-item {{ request()->is('documents*') ? 'active' : '' }}" data-module="documents">
            <a href="{{ url('/documents') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-file-alt nav-icon"></i>
                <span class="nav-label">Documents</span>
            </a>
        </div>

        {{-- <div class="nav-item {{ request()->is('notifications*') ? 'active' : '' }}" data-module="notifications">
            <a href="{{ url('/notifications') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-bell nav-icon"></i>
                <span class="nav-label">Notifications</span>
            </a>
        </div> --}}

        <div class="nav-item {{ request()->is('admin*') ? 'active' : '' }}" data-module="admin">
            <a href="{{ url('/admin') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-setting nav-icon"></i>
                <span class="nav-label">Administration</span>
            </a>
        </div>
    </nav>
</div>
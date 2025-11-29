<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('logo.png') }}" alt="logo">
    </div>

    <nav class="nav-menu">
        {{-- Top-level primary modules (kept in same order as original index.html) --}}
        <div class="nav-item {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" data-module="dashboard">
            <a href="{{ url('/dashboard') }}" class="nav-button">
                <i class="uil uil-estate nav-icon"></i>
                <span class="nav-label">@lang('sidebar.dashboard')</span>
            </a>
        </div>
        @if(Auth::user()->can('View Customer') || Auth::user()->can('View Quotes'))
        <div class="nav-item {{ request()->is('crm*') ? 'active' : '' }}" data-module="crm">
            <a href="{{ url('/customers') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-users-alt nav-icon"></i>
                <span class="nav-label">@lang('sidebar.crm')</span>
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View R&D'))
        <div class="nav-item {{ request()->is('rnd*') ? 'active' : '' }}" data-module="documents">
            <a href="{{ url('/rnd') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-file-alt nav-icon"></i>
                <span class="nav-label">@lang('sidebar.rnd')</span>
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View Quality & Control'))
        <div class="nav-item {{ request()->is('qa*') ? 'active' : '' }}" data-module="documents">
            <a href="{{ url('/qa') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-file-alt nav-icon"></i>
                <span class="nav-label">@lang('sidebar.quality_control')</span>
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View Orders'))
        <div class="nav-item {{ request()->is('orders*') ? 'active' : '' }}" data-module="orders">
            <a href="{{ url('/orders') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-receipt nav-icon"></i>
                <span class="nav-label">@lang('sidebar.orders')</span>
                {{-- optional dynamic badge for pending invoices --}}
                {{-- <span style="margin-left:auto; background:#f59e0b; color:#fff; border-radius:12px; padding:2px 8px; font-size:11px; font-weight:700;" id="nav-orders-pending">{{ $navCounts['orders_pending'] ?? '' }}</span> --}}
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View Production'))
        <div class="nav-item {{ request()->is('production*') ? 'active' : '' }}" data-module="production">
            <a href="{{ url('/production') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-cube nav-icon"></i>
                <span class="nav-label">@lang('sidebar.production')</span>
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View Invoices'))
        <div class="nav-item {{ request()->is('invoices*') ? 'active' : '' }}" data-module="invoices">
            <a href="{{ url('/invoices') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-invoice nav-icon"></i>
                <span class="nav-label">@lang('sidebar.invoicing')</span>
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View Inventory'))
        <div class="nav-item {{ request()->is('inventory*') ? 'active' : '' }}" data-module="inventory">
            <a href="{{ url('/inventory') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-box nav-icon"></i>
                <span class="nav-label">@lang('sidebar.inventory')</span>
            </a>
        </div>
        @endif
        @if(Auth::user()->can('View Products'))
        <div class="nav-item {{ request()->is('products*') ? 'active' : '' }}" data-module="products">
            <a href="{{ url('/products') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-shopping-bag nav-icon"></i>
                <span class="nav-label">@lang('sidebar.products')</span>
            </a>
        </div>
        @endif

        @if(Auth::user()->can('View Users'))
                <div class="nav-item" data-module="users">
                    <a href="{{ route_if_exists('users.index') ?: url('/users') }}" class="nav-button">
                        <i class="uil uil-user nav-icon"></i>
                        <span class="nav-label">@lang('sidebar.users')</span>
                    </a>
                </div>
        @endif

        @if(Auth::user()->can('View Tasks'))

        <div class="nav-item {{ request()->is('workflow*') ? 'active' : '' }}" data-module="workflow">
            <a href="{{ url('/workflow') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-sitemap nav-icon"></i>
                <span class="nav-label">@lang('sidebar.workflow')</span>
                {{-- small badge for tasks/notifications if needed --}}
            </a>
        </div>
        @endif

        <div class="nav-item {{ request()->is('admin*') ? 'active' : '' }}" data-module="admin">
            <a href="{{ url('/admin') }}" class="nav-button" aria-expanded="false">
                <i class="uil uil-setting nav-icon"></i>
                <span class="nav-label">@lang('sidebar.administration')</span>
            </a>
        </div>
    </nav>
</div>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'White Industry ERP System')</title>

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-M9hm+yDy2G0hZcW0D1M7RfZbC7N7aMZ+nXkmDLMKf6h0eCEFuS/j6m9XHb9k8+3L"
      crossorigin="anonymous">
      <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('css/manage.css') }}">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">



</head>

<body>



    {{-- Server-side flash placeholder removed in favor of unified toaster calls below --}}


    <!-- Early minimal fallbacks to prevent ReferenceError for modal/toast/confirm helpers
         These will create the same DOM structure used by the full implementation so callers
         during page startup receive consistent visuals (icons, close button) even before
         the full script finishes parsing. The full implementations later will override these.
    -->

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
     <script src="{{ asset('js/custom.js') }}"></script>
    <script>
  

        // Early showToaster fallback: creates the same DOM structure (icon, title, message, close)
        if (!window.showToaster) window.showToaster = function(type, title, message, duration) {
            try {
                type = String(type || 'info');
                title = String(title || '');
                message = String(message || '');
                let container = document.getElementById('toaster-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toaster-container';
                    container.className = 'toaster-container';
                    // inline styles to ensure visibility during early load
                    container.style.position = 'fixed';
                    container.style.top = '20px';
                    container.style.right = '20px';
                    container.style.zIndex = '99999';
                    container.style.display = 'flex';
                    container.style.flexDirection = 'column';
                    container.style.gap = '10px';
                    container.style.maxWidth = '360px';
                    document.body.appendChild(container);
                }

                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'ℹ'
                };
                const icon = icons[type] || icons.info;

                const esc = (s) => String(s).replace(/[&<>'"`]/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '\'': '&#39;',
                '"': '&quot;',
                '`': '&#96;'
                } [c] || c));

                const t = document.createElement('div');
                t.className = `toaster ${type}`;
                t.setAttribute('role', 'status');
                t.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

                t.innerHTML =
                    `\n                    <div class="toaster-icon">${icon}</div>\n                    <div class="toaster-content">\n                        <div class="toaster-title">${esc(title)}</div>\n                        <div class="toaster-message">${esc(message)}</div>\n                    </div>\n                    <button class="toaster-close" aria-label="Close" type="button">×</button>\n                `;

                container.appendChild(t);
                // attach basic removal handler for early fallback
                const closeBtn = t.querySelector('.toaster-close');
                if (closeBtn) closeBtn.addEventListener('click', function() {
                    t.classList.remove('show');
                    setTimeout(() => {
                        try {
                            t.remove();
                        } catch (e) {}
                    }, 260);
                });

                setTimeout(() => t.classList.add('show'), 50);
                setTimeout(() => {
                    try {
                        t.classList.remove('show');
                        setTimeout(() => {
                            try {
                                t.remove();
                            } catch (e) {}
                        }, 300);
                    } catch (e) {}
                }, Math.max(1500, duration || 3500));
            } catch (e) {
                try {
                    console.log('early showToaster failed', e);
                } catch (_) {}
            }
        };

        // Safe no-op placeholders for globals that pages may call via inline onclick before
        // the full layout script has finished parsing. They will be replaced by the real
        // implementations later in the file.
        if (!window.createItem) window.createItem = function(type) {
            console.warn('createItem called before full script loaded');
        };
        if (!window.showTab) window.showTab = function(moduleId, tabId, clickedElement) {
            console.warn('showTab called before full script loaded');
        };
        if (!window.showModule) window.showModule = function(moduleId) {
            console.warn('showModule called before full script loaded');
        };

        // Early showConfirm: prefer the modal overlay if present, otherwise fallback to native confirm
        if (!window.showConfirm) window.showConfirm = function(message, title) {
            return new Promise((resolve) => {
                try {
                    const overlay = document.getElementById('modal-overlay');
                    if (overlay) {
                        const modalTitle = document.getElementById('modal-title');
                        const modalBody = document.getElementById('modal-body');
                        const modalContent = document.querySelector('#modal-overlay .modal-content');
                        modalTitle.textContent = title || 'Confirm Deletion';
                        // set header into confirm style by adding class
                        if (modalContent) modalContent.classList.add('confirm');
                        // build centered alert layout
                        modalBody.innerHTML =
                            `\n                            <div class="confirm-alert-icon" aria-hidden>\n                                <div class="confirm-triangle">\n                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n                                        <path d="M12 9v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>\n                                        <path d="M12 15h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>\n                                        <path d="M10.29 3.86L3.46 18.06a2 2 0 0 0 1.73 3h13.62a2 2 0 0 0 1.73-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>\n                                    </svg>\n                                </div>\n                            </div>\n                            <div class="confirm-message">${String(message || 'Are you sure you want to delete this item? This action cannot be undone.')}</div>\n                            <div class="confirm-actions">\n                                <button id="early-confirm-no" class="btn btn-secondary">\u00D7 Cancel</button>\n                                <button id="early-confirm-yes" class="btn btn-danger"><svg style="width:16px;height:16px;margin-right:8px;vertical-align:middle" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 6v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 6l1-2h4l1 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Delete</button>\n                            </div>\n                        `;
                        overlay.classList.add('show');

                        function cleanup() {
                            try {
                                const yesBtn = document.getElementById('early-confirm-yes');
                                const noBtn = document.getElementById('early-confirm-no');
                                if (noBtn) noBtn.removeEventListener('click', onNo);
                                if (yesBtn) yesBtn.removeEventListener('click', onYes);
                                if (modalContent) modalContent.classList.remove('confirm');
                                overlay.classList.remove('show');
                            } catch (e) {}
                        }

                        function onYes() {
                            try {
                                const text = String(message || title || '');
                                if (/delete/i.test(text)) {
                                    try {
                                        if (typeof showToaster === 'function') showToaster('success', 'Deleted',
                                            'Item deletion confirmed');
                                    } catch (e) {}
                                }
                            } catch (e) {}
                            cleanup();
                            resolve(true);
                        }

                        function onNo() {
                            cleanup();
                            resolve(false);
                        }
                        const yesBtn = document.getElementById('early-confirm-yes');
                        const noBtn = document.getElementById('early-confirm-no');
                        if (yesBtn) yesBtn.addEventListener('click', onYes);
                        if (noBtn) noBtn.addEventListener('click', onNo);
                        return;
                    }
                    // fallback to native confirm if modal not present
                    const result = window.confirm ? window.confirm(message || title || 'Confirm?') : false;
                    resolve(Boolean(result));
                } catch (e) {
                    console.error('early showConfirm error', e);
                    resolve(false);
                }
            });
        };

        if (!window.showModal) window.showModal = function() {
            try {
                const o = document.getElementById('modal-overlay');
                if (o) {
                    o.style.zIndex = 100000;
                    o.classList.add('show');
                    o.style.display = 'flex';
                }
            } catch (e) {}
        };
        if (!window.hideModal) window.hideModal = function() {
            try {
                const o = document.getElementById('modal-overlay');
                if (o) {
                    const modalContent = o.querySelector('.modal-content');
                    if (modalContent && modalContent.classList.contains('confirm')) modalContent.classList.remove(
                        'confirm');
                    o.classList.remove('show');
                    o.style.display = 'none';
                }
            } catch (e) {}
        };

        // define testToasters early so it's callable immediately from console
        if (!window.testToasters) {
            window.testToasters = function() {
                try {
                    if (typeof window.showToaster !== 'function') {
                        console.error('early testToasters: showToaster not a function');
                        return;
                    }
                    showToaster('info', 'Info', 'Early test toaster (info)', 2000);
                    setTimeout(() => showToaster('success', 'Success', 'Early success', 2500), 300);
                    setTimeout(() => showToaster('warning', 'Warning', 'Early warning', 3000), 600);
                    setTimeout(() => showToaster('error', 'Error', 'Early error', 3500), 900);
                } catch (e) {
                    console.error('early testToasters failed', e);
                }
            };
        }
    </script>
    <div class="">
        @include('partials.sidebar')

        <div class="main-content">
            @include('partials.header')

            <main class="content">

                <!-- Server flash toasts are shown via the app's custom showToaster() to avoid depending on Bootstrap markup -->
                        {{-- Render any server-side flash messages using the app's showToaster function so all toasts are consistent. --}}
        @if(session()->has('toasts'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    try {
                        const toasts = @json(session('toasts')) || [];
                        toasts.forEach(t => {
                            try { showToaster(t.type || 'info', t.title || '', t.message || '', t.duration || 4000); } catch(e){}
                        });
                    } catch (e) { console.error('rendering session toasts failed', e); }
                });
            </script>
        @else
            @php
                $flashType = null;
                $flashMessage = null;
                if (session()->has('error')) { $flashType = 'error'; $flashMessage = session('error'); }
                elseif (session()->has('success')) { $flashType = 'success'; $flashMessage = session('success'); }
                elseif (session()->has('warning')) { $flashType = 'warning'; $flashMessage = session('warning'); }
                elseif (session()->has('message')) { $flashType = 'info'; $flashMessage = session('message'); }
            @endphp
            @if($flashType && $flashMessage)
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        try {
                            if (typeof window.showToaster === 'function') {
                                window.showToaster(@json($flashType), @json(ucfirst($flashType)), @json($flashMessage), 5000);
                            } else {
                                // fallback: simple alert
                                alert(@json($flashMessage));
                            }
                        } catch (e) { console.error('server flash toaster failed', e); }
                    });
                </script>
            @endif
        @endif



                @yield('content')
            </main>
        </div>
    </div>

    <div id="toaster-container" class="toaster-container"></div>

    <div id="commonModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title" class="modal-title">Modal Title</h3>
                <button class="close" onclick="hideModel()">&times;</button>
            </div>
            <div id="body" class="body"></div>
        </div>
    </div>

     <div id="modal-overlay" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title" class="modal-title">Modal Title</h3>
                <button class="close" onclick="hideModel()">&times;</button>
            </div>
            <div id="modal-body" class="body"></div>
        </div>
    </div>
   
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script>

          function hideModel(){
    $("#commonModal").css('display','none');
}
        // Initialize Bootstrap toast if present. Retry briefly if bootstrap isn't loaded yet.
        document.addEventListener('DOMContentLoaded', function() {
            function initAppToast() {
                const toastEl = document.getElementById('appToast');
                if (!toastEl) return; // nothing to do

                if (typeof bootstrap === 'undefined' || typeof bootstrap.Toast !== 'function') {
                    // bootstrap not available yet; try again shortly
                    setTimeout(initAppToast, 100);
                    return;
                }

                try {
                    const toast = new bootstrap.Toast(toastEl, {
                        delay: 4000,
                        autohide: true
                    });
                    toast.show();
                } catch (e) {
                    console.warn('Bootstrap toast init failed', e);
                }
            }

            initAppToast();
        });
    </script>


    <script>
 


        function showModule(moduleId) {
            document.querySelectorAll('.module').forEach(module => {
                module.classList.remove('active');
            });
            const targetModule = document.getElementById(moduleId);
            if (targetModule) {
                targetModule.classList.add('active');
            }
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-module') === moduleId) {
                    item.classList.add('active');
                }
            });
            const titles = {
                'dashboard': 'Dashboard',
                'crm': 'Customer Relationship Management',
                'production': 'Production Management',
                'inventory': 'Inventory Management',
                'products': 'Product Management',
                'orders': 'Order Management',
                'invoicing': 'Invoicing & Payments',
                'workflow': 'Workflow Management',
                'documents': 'Document Management',
                'notifications': 'Notifications',
                'admin': 'System Administration'
            };
            const titleElement = document.getElementById('page-title');
            if (titleElement) {
                titleElement.textContent = titles[moduleId] || 'Dashboard';
            }
        }

        function showTab(moduleId, tabId, clickedElement) {
            // First, try the conventional structure: a container with id=moduleId that has .tab-content children
            const moduleContainer = document.getElementById(moduleId);
            if (moduleContainer) {
                moduleContainer.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
            } else {
                // Fallback: remove .active from any tab-content whose id starts with `${moduleId}-`
                document.querySelectorAll('.tab-content').forEach(content => {
                    if (content.id && content.id.indexOf(moduleId + '-') === 0) content.classList.remove('active');
                });
            }

            const targetTab = document.getElementById(`${moduleId}-${tabId}`);
            if (targetTab) {
                targetTab.classList.add('active');
            }

            // Toggle active state for tab buttons: scoped inside module when possible, otherwise globally for buttons that reference this module
            if (moduleContainer) {
                moduleContainer.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('active');
                });
            } else {
                document.querySelectorAll('.tab-button').forEach(button => {
                    // If button's onclick references the moduleId, clear active — best-effort
                    try {
                        const on = button.getAttribute('onclick') || '';
                        if (on.indexOf("'" + moduleId + "'") !== -1 || on.indexOf('"' + moduleId + '"') !== -1)
                            button.classList.remove('active');
                    } catch (e) {}
                });
            }

            if (clickedElement) {
                clickedElement.classList.add('active');
            }
        }

       

        // Global Quick Add helper so header Quick Add works on any page
    

        function quickAddItem(type) {
            hideModal();
            setTimeout(() => {
                if (window.createItem) window.createItem(type);
                else createItem(type);
            }, 100);
        }

        // expose quick add helpers globally
        window.showQuickAdd = showQuickAdd;
        window.quickAddItem = quickAddItem;
        window.filterQuickActions = filterQuickActions;

        function showModal() {
            const o = document.getElementById('modal-overlay');
            if (!o) return;
            o.style.zIndex = 100000;
            o.style.display = 'flex';
            o.classList.add('show');
        }

        function hideModal() {
            const o = document.getElementById('modal-overlay');
            if (!o) return;
            const modalContent = o.querySelector('.modal-content');
            if (modalContent && modalContent.classList.contains('confirm')) modalContent.classList.remove('confirm');
            o.classList.remove('show');
            o.style.display = 'none';
        }

        // Global helper to view order details in a modal. Defined here so any page can open order modals
  
        function formatTitle(type) {
            return type.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        function submitForm(type) {
            hideModal();
            showToaster('success', 'Success!', `${formatTitle(type)} created successfully!`);
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Attach SPA-style handlers only for nav items that use a <button> (legacy single-page layout).
            document.querySelectorAll('.nav-item').forEach(item => {
                const navButton = item.querySelector('.nav-button');
                if (!navButton) return;
                // If the nav-button is a <button> (not an anchor), intercept clicks and show the module.
                if (navButton.tagName === 'BUTTON') {
                    navButton.addEventListener('click', function(e) {
                        const moduleId = item.getAttribute('data-module');
                        if (moduleId) {
                            e.preventDefault();
                            showModule(moduleId);
                        }
                    });
                }
            });

            // Close modal when clicking outside the modal-content
            const modalOverlay = document.getElementById('modal-overlay');
            if (modalOverlay) {
                modalOverlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideModal();
                    }
                });
            }

            // Ensure header Quick Add / New Order buttons use global handlers (works across pages)
            const quickBtn = document.getElementById('header-quick-add-btn');
            if (quickBtn) quickBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.showQuickAdd) window.showQuickAdd();
                else if (typeof showQuickAdd === 'function') showQuickAdd();
            });
            const orderBtn = document.getElementById('header-new-order-btn');
            if (orderBtn) orderBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.createItem) window.createItem('order');
                else if (typeof createItem === 'function') createItem('order');
            });
        });
    </script>

    <script>
        // Sidebar collapse toggle and persistence
        (function() {
            const toggle = document.getElementById('sidebar-toggle');
            const root = document.documentElement; // add class to <html> for CSS scope
            const STORAGE_KEY = 'wi_sidebar_collapsed';

            function setCollapsed(collapsed) {
                if (collapsed) {
                    document.body.classList.add('sidebar-collapsed');
                    toggle.setAttribute('aria-pressed', 'true');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                    toggle.setAttribute('aria-pressed', 'false');
                }
            }

            // Initialize from localStorage
            const saved = localStorage.getItem(STORAGE_KEY);
            setCollapsed(saved === '1');

            if (toggle) {
                toggle.addEventListener('click', function() {
                    const collapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
                    toggle.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
                });
            }
        })();
    </script>

   

    @stack('scripts')

    @yield('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MZsJ+KJKR8VnUtZbMBqff7vZJf+W9f9PGdG7gqkQMTfNRBpdWOVZ9Is6m3ti+rJ8"
        crossorigin="anonymous"></script>
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

</body>

</html>

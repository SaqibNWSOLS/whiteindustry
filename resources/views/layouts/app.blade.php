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
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">


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
    <div class="container">
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
        // Copied JavaScript (kept inline for quick conversion). You can move to a separate JS file later.
        function showToaster(type, title, message, duration = 4000) {
            try {
                // debug: log invocation so we can trace calls
                if (window && window.console && typeof window.console.debug === 'function') {
                    console.debug('showToaster called', {
                        type,
                        title,
                        message,
                        duration
                    });
                }
                // normalize inputs
                type = String(type || 'info');
                title = String(title || '');
                message = String(message || '');

                // ensure container exists
                let container = document.getElementById('toaster-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toaster-container';
                    container.className = 'toaster-container';
                    // ensure it's visible above other elements
                    container.style.position = container.style.position || 'fixed';
                    container.style.top = container.style.top || '20px';
                    container.style.right = container.style.right || '20px';
                    container.style.zIndex = container.style.zIndex || '99999';
                    document.body.appendChild(container);
                } else {
                    // ensure container is visible
                    try {
                        container.style.zIndex = container.style.zIndex || '99999';
                    } catch (e) {}
                }

                const toaster = document.createElement('div');
                toaster.className = `toaster ${type}`;
                toaster.setAttribute('role', 'status');
                toaster.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'ℹ'
                };
                const icon = icons[type] || icons.info;

                // escape plain text to avoid accidental HTML injection
                const esc = (s) => String(s).replace(/[&<>'"`]/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '\'': '&#39;',
                '"': '&quot;',
                '`': '&#96;'
                } [c] || c));

                toaster.innerHTML =
                    `\n                    <div class="toaster-icon">${icon}</div>\n                    <div class="toaster-content">\n                        <div class="toaster-title">${esc(title)}</div>\n                        <div class="toaster-message">${esc(message)}</div>\n                    </div>\n                    <button class="toaster-close" aria-label="Close" type="button">×</button>\n                `;

                container.appendChild(toaster);
                // debug: show resulting DOM state
                if (window && window.console && typeof window.console.debug === 'function') {
                    console.debug('toaster appended, container children count=', container.children.length);
                }

                // wire close button
                const closeBtn = toaster.querySelector('.toaster-close');
                if (closeBtn) closeBtn.addEventListener('click', () => removeToaster(toaster));

                // animate in and schedule removal
                setTimeout(() => toaster.classList.add('show'), 50);
                const timeout = Math.max(1500, Number(duration) || 4000);
                setTimeout(() => removeToaster(toaster), timeout);
            } catch (e) {
                try {
                    console.error('showToaster error', e);
                } catch (err) {}
            }
        }

        // expose toaster globally and protect it from accidental overwrites
        try {
            Object.defineProperty(window, 'showToaster', {
                value: showToaster,
                writable: false,
                configurable: false
            });
        } catch (e) {
            // older browsers or if already defined, fall back to simple assignment
            window.showToaster = showToaster;
        }


        // modern confirm modal helper that returns a Promise<boolean>
        function showConfirm(message, title = 'Confirm Deletion') {
            return new Promise((resolve) => {
                const modalTitle = document.getElementById('modal-title');
                const modalBody = document.getElementById('modal-body');
                const modalContent = document.querySelector('#modal-overlay .modal-content');
                modalTitle.textContent = title;
                // apply confirm class for gradient header and compact look
                if (modalContent) modalContent.classList.add('confirm');
                // centered alert layout (icon, message, centered actions)
                modalBody.innerHTML =
                    `\n                    <div class="confirm-alert-icon" aria-hidden>\n                        <div class="confirm-triangle">\n                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n                                <path d="M12 9v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>\n                                <path d="M12 15h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>\n                                <path d="M10.29 3.86L3.46 18.06a2 2 0 0 0 1.73 3h13.62a2 2 0 0 0 1.73-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>\n                            </svg>\n                        </div>\n                    </div>\n                    <div class="confirm-message">${String(message || 'Are you sure you want to delete this item? This action cannot be undone.')}</div>\n                    <div class="confirm-actions">\n                        <button id="confirm-no" class="btn btn-secondary">\u00D7 Cancel</button>\n                        <button id="confirm-yes" class="btn btn-danger"><svg style="width:16px;height:16px;margin-right:8px;vertical-align:middle" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 6v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 6l1-2h4l1 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Delete</button>\n                    </div>\n                `;

                showModal();

                function cleanup() {
                    try {
                        const yes = document.getElementById('confirm-yes');
                        const no = document.getElementById('confirm-no');
                        if (yes) yes.removeEventListener('click', onYes);
                        if (no) no.removeEventListener('click', onNo);
                        if (modalContent) modalContent.classList.remove('confirm');
                        hideModal();
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
                const yesBtn = document.getElementById('confirm-yes');
                const noBtn = document.getElementById('confirm-no');
                if (yesBtn) yesBtn.addEventListener('click', onYes);
                if (noBtn) noBtn.addEventListener('click', onNo);
            });
        }
        try {
            Object.defineProperty(window, 'showConfirm', {
                value: showConfirm,
                writable: false,
                configurable: false
            });
        } catch (e) {
            window.showConfirm = showConfirm;
        }

        function removeToaster(toaster) {
            if (toaster && toaster.parentElement) {
                toaster.classList.remove('show');
                setTimeout(() => {
                    if (toaster.parentElement) {
                        toaster.parentElement.removeChild(toaster);
                    }
                }, 300);
            }
        }

        // expose removeToaster for advanced use / testing and protect it
        try {
            Object.defineProperty(window, 'removeToaster', {
                value: removeToaster,
                writable: false,
                configurable: false
            });
        } catch (e) {
            window.removeToaster = removeToaster;
        }

        // small helper to test toasters visually from the console
        window.testToasters = function() {
            try {
                console.debug('testToasters invoked, showToaster type:', typeof window.showToaster);
                if (typeof window.showToaster !== 'function') {
                    console.error('testToasters: showToaster is not a function', window.showToaster);
                    return;
                }
                showToaster('info', 'Info', 'This is an info toaster — quick test', 2500);
                setTimeout(() => showToaster('success', 'Success', 'Operation completed successfully', 3000), 350);
                setTimeout(() => showToaster('warning', 'Warning', 'Check this', 3500), 700);
                setTimeout(() => showToaster('error', 'Error', 'There was an error', 4000), 1050);
            } catch (e) {
                console.error('testToasters failed', e);
            }
        };
        // protect testToasters as well
        try {
            Object.defineProperty(window, 'testToasters', {
                value: window.testToasters,
                writable: false,
                configurable: false
            });
        } catch (e) {}

        // small helper to test the confirm modal from console: returns a Promise<boolean>
        window.testConfirm = async function() {
            try {
                const result = await showConfirm('This is a test delete confirmation. Do you want to proceed?',
                    'Confirm deletion');
                console.log('testConfirm result:', result);
                showToaster(result ? 'success' : 'info', 'Confirm result', result ? 'You confirmed' :
                    'You cancelled');
                return result;
            } catch (e) {
                console.error('testConfirm failed', e);
                return false;
            }
        };
        try {
            Object.defineProperty(window, 'testConfirm', {
                value: window.testConfirm,
                writable: false,
                configurable: false
            });
        } catch (e) {}

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

        async function createItem(type, presetCategory) {
            // Build richer quick-add modals for common types so Quick Add works everywhere
            showModal();
            const title = `Create New ${type.split('-').map(w => w.charAt(0).toUpperCase()+w.slice(1)).join(' ')}`;
            document.getElementById('modal-title').textContent = title;
            const body = document.getElementById('modal-body');

            async function submitJson(url, data, successMsg) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch(url, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(data)
                    });
                    if (!res.ok) {
                        const err = await res.json().catch(() => null);
                        throw new Error(err?.message || res.statusText || 'Request failed');
                    }
                    hideModal();
                    showToaster('success', 'Created', successMsg || 'Item created');
                    // If on a module that lists these items, try to refresh by calling known loaders
                    if (typeof loadCustomers === 'function') try {
                        loadCustomers();
                    } catch (e) {}
                    if (typeof loadLeads === 'function') try {
                        loadLeads();
                    } catch (e) {}
                    if (typeof loadQuotes === 'function') try {
                        loadQuotes();
                    } catch (e) {}
                    if (typeof loadOrders === 'function') try {
                        loadOrders();
                    } catch (e) {}
                    // invoicing-specific loaders
                    if (typeof loadInvoices === 'function') try {
                        loadInvoices();
                    } catch (e) {}
                    if (typeof loadPayments === 'function') try {
                        loadPayments();
                    } catch (e) {}
                    if (typeof loadInvoicingStats === 'function') try {
                        loadInvoicingStats();
                    } catch (e) {}
                    if (typeof loadPaymentStats === 'function') try {
                        loadPaymentStats();
                    } catch (e) {}
                    if (typeof loadTracking === 'function') try {
                        loadTracking();
                    } catch (e) {}
                    // inventory specific
                    if (typeof loadInventoryAll === 'function') try {
                        loadInventoryAll();
                    } catch (e) {}
                } catch (err) {
                    showToaster('error', 'Error', err.message);
                }
            }

            if (type === 'customer') {
                body.innerHTML = `
                    <form id="qa-customer-form">
                        <div class="form-group"><label class="form-label">Type</label>
                            <select id="qa-customer-type" name="type" class="form-input"><option value="business">Business</option><option value="person">Person</option></select>
                        </div>
                        <div id="qa-business-fields">
                            <div class="form-group"><label class="form-label">Company Name</label><input name="company_name" class="form-input" /></div>
                            <div class="form-group"><label class="form-label">Industry Type</label>
                                <select name="industry_type" class="form-input"><option value="">Select industry</option><option value="Cosmetics & Beauty">Cosmetics & Beauty</option><option value="Pharmaceuticals">Pharmaceuticals</option><option value="Dietary Supplements">Dietary Supplements</option><option value="Other">Other</option></select>
                            </div>
                            <div class="form-group"><label class="form-label">Tax ID</label><input name="tax_id" class="form-input" /></div>
                        </div>
                        <div class="form-group"><label class="form-label">Contact Person</label><input name="contact_person" class="form-input" required /></div>
                        <div class="form-group"><label class="form-label">Email</label><input name="email" type="email" class="form-input" required /></div>
                        <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-input" required /></div>
                        <div class="form-group"><label class="form-label">Address</label><input name="address" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">City</label><input name="city" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">Postal Code</label><input name="postal_code" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-input"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                        <div style="display:flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;"><button type="submit" class="btn btn-primary">Create</button><button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;

                const form = document.getElementById('qa-customer-form');
                const typeSelect = document.getElementById('qa-customer-type');
                const businessFields = document.getElementById('qa-business-fields');

                function toggle() {
                    if (typeSelect.value === 'business') {
                        businessFields.style.display = '';
                        if (form.company_name) form.company_name.required = true;
                    } else {
                        businessFields.style.display = 'none';
                        ['company_name', 'industry_type', 'tax_id'].forEach(n => {
                            if (form[n]) {
                                form[n].value = '';
                                form[n].required = false;
                            }
                        });
                    }
                }
                toggle();
                typeSelect.addEventListener('change', toggle);
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = Object.fromEntries(new FormData(form).entries());
                    submitJson('/api/customers', data, 'Customer created');
                });
                return;
            }

            if (type === 'lead') {
                body.innerHTML = `
                    <form id="qa-lead-form">
                        <div class="form-group"><label class="form-label">Source</label><select name="source" class="form-input"><option value="website">Website</option><option value="referral">Referral</option><option value="trade_show">Trade Show</option><option value="cold_call">Cold Call</option><option value="social_media">Social Media</option></select></div>
                        <div class="form-group"><label class="form-label">Company Name</label><input name="company_name" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">Contact Person</label><input name="contact_person" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">Email</label><input name="email" type="email" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-input" /></div>
                        <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-input"><option value="new">New</option><option value="contacted">Contacted</option><option value="qualified">Qualified</option><option value="converted">Converted</option></select></div>
                        <div style="display:flex; gap:12px; margin-top:12px;"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-secondary" type="button" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;
                const form = document.getElementById('qa-lead-form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = Object.fromEntries(new FormData(form).entries());
                    submitJson('/api/leads', data, 'Lead created');
                });
                return;
            }

            if (type === 'quote') {
                // Need customers and products for selects
                body.innerHTML = `<div>Loading...</div>`;
                const [custRes, prodRes] = await Promise.all([fetch('/api/customers', {
                    credentials: 'same-origin'
                }), fetch('/api/products', {
                    credentials: 'same-origin'
                })]);
                const customers = custRes.ok ? (await custRes.json()).data || [] : [];
                const products = prodRes.ok ? (await prodRes.json()).data || [] : [];
                body.innerHTML = `
                    <form id="qa-quote-form">
                        <div class="form-group"><label class="form-label">Customer</label><select name="customer_id" class="form-input">${customers.map(c=>`<option value="${c.id}">${(c.company_name||c.contact_person||c.id)}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Product</label><select name="product_id" class="form-input">${products.map(p=>`<option value="${p.id}">${(p.name||p.product_code||p.id)}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Quantity</label><input name="quantity" class="form-input" type="number" step="0.001" required /></div>
                        <div class="form-group"><label class="form-label">Unit</label><input name="unit" class="form-input" required /></div>
                        <div class="form-group"><label class="form-label">Unit Price</label><input name="unit_price" class="form-input" type="number" step="0.01" required /></div>
                        <div class="form-group"><label class="form-label">Total Amount</label><input name="total_amount" class="form-input" type="number" step="0.01" required /></div>
                        <div class="form-group"><label class="form-label">Valid Until</label><input name="valid_until" class="form-input" type="date" required /></div>
                        <div style="display:flex; gap:12px; margin-top:12px;"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-secondary" type="button" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;
                const form = document.getElementById('qa-quote-form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = Object.fromEntries(new FormData(form).entries());
                    submitJson('/api/quotes', data, 'Quote created');
                });
                return;
            }

            if (type === 'order') {
                body.innerHTML = `<div>Loading...</div>`;
                // fetch customers and products
                const [custRes, prodRes] = await Promise.all([fetch('/api/customers', {
                    credentials: 'same-origin'
                }), fetch('/api/products', {
                    credentials: 'same-origin'
                })]);
                const customers = custRes.ok ? (await custRes.json()).data || [] : [];
                const products = prodRes.ok ? (await prodRes.json()).data || [] : [];
                body.innerHTML = `
                    <form id="qa-order-form">
                        <div class="form-group"><label class="form-label">Customer</label><select name="customer_id" class="form-input">${customers.map(c=>`<option value="${c.id}">${(c.company_name||c.contact_person||c.id)}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Product</label><select name="product_id" class="form-input">${products.map(p=>`<option value="${p.id}">${(p.name||p.product_code||p.id)}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Quantity</label><input name="quantity" class="form-input" type="number" step="0.001" required /></div>
                        <div class="form-group"><label class="form-label">Unit</label><input name="unit" class="form-input" required /></div>
                        <div class="form-group"><label class="form-label">Unit Price</label><input name="unit_price" class="form-input" type="number" step="0.01" required /></div>
                        <div class="form-group"><label class="form-label">Total Amount</label><input name="total_amount" class="form-input" type="number" step="0.01" required /></div>
                        <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-input"><option value="pending">pending</option><option value="confirmed">confirmed</option><option value="shipped">shipped</option></select></div>
                        <div style="display:flex; gap:12px; margin-top:12px;"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-secondary" type="button" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;
                const form = document.getElementById('qa-order-form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = Object.fromEntries(new FormData(form).entries());
                    submitJson('/api/orders', data, 'Order created');
                });
                return;
            }

            if (type === 'product') {
                // Create Inventory quick-add modal (store into inventory table)
                // categories and units can be fetched from server if endpoints exist; fallback to static lists
                const categories = ['Select category', 'Active Ingredient', 'Base Material', 'Preservative',
                    'Fragrance', 'Container', 'Accessory'
                ];
                const units = ['L', 'kg', 'pcs', 'g', 'ml'];
                const typeOptions = [{
                        label: 'Raw Material',
                        value: 'raw_material'
                    },
                    {
                        label: 'Packaging',
                        value: 'packaging'
                    },
                    {
                        label: 'Final Product',
                        value: 'final_product'
                    }
                ];

                body.innerHTML = `
                    <form id="qa-product-form">
                        <div class="form-group"><label class="form-label">Category</label><select name="category" id="qa-product-category" class="form-input">${categories.map(c=>`<option value="${c==='Select category'?'':c}" ${((presetCategory && presetCategory===c)?'selected':'')}>${c}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Material Code</label><input name="material_code" class="form-input" placeholder="MAT-XXX-001" /></div>
                        <div class="form-group"><label class="form-label">Name</label><input name="name" class="form-input" placeholder="Item name" required /></div>
                        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-input" rows="3" placeholder="Description (optional)"></textarea></div>
                        <div style="display:flex;gap:12px;"><div style="flex:1" class="form-group"><label class="form-label">Current Stock</label><input name="current_stock" type="number" step="0.001" class="form-input" value="0" /></div><div style="width:200px" class="form-group"><label class="form-label">Minimum Stock</label><input name="minimum_stock" type="number" step="0.001" class="form-input" value="0" /></div></div>
                        <div style="display:flex;gap:12px;"><div style="flex:1" class="form-group"><label class="form-label">Unit Cost (DZD)</label><input name="unit_cost" type="number" step="0.01" class="form-input" value="0.00" /></div><div style="width:200px" class="form-group"><label class="form-label">Unit</label><select name="unit" class="form-input">${units.map(u=>`<option value="${u}">${u}</option>`).join('')}</select></div></div>
                        <div class="form-group"><label class="form-label">Type</label><select id="qa-product-type" name="type" class="form-input">${typeOptions.map(t=>`<option value="${t.value}" ${(presetCategory && presetCategory.toLowerCase() === t.label.toLowerCase()) ? 'selected' : ''}>${t.label}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Supplier</label><input name="supplier" class="form-input" placeholder="Supplier name" /></div>
                        <div class="form-group"><label class="form-label">Storage Location</label><input name="storage_location" class="form-input" placeholder="Warehouse / shelf" /></div>
                        <div style="display:flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;"><button type="submit" class="btn btn-primary">Create Item</button><button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;
                const pform = document.getElementById('qa-product-form');
                pform.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const raw = Object.fromEntries(new FormData(pform).entries());
                    // normalize empty strings to null where appropriate
                    const data = Object.assign({}, raw);
                    if (data.category === '') delete data.category;
                    // ensure numeric fields are numbers
                    if (data.current_stock !== undefined) data.current_stock = Number(data.current_stock || 0);
                    if (data.minimum_stock !== undefined) data.minimum_stock = Number(data.minimum_stock || 0);
                    if (data.unit_cost !== undefined) data.unit_cost = Number(data.unit_cost || 0);
                    submitJson('/api/inventory', data, 'Inventory item created');
                });
                return;
            }

            if (type === 'invoice') {
                body.innerHTML = `<div>Loading...</div>`;
                // Need customers and optionally orders for selected customer
                const custRes = await fetch('/api/customers', {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const customers = custRes.ok ? (await custRes.json()).data || [] : [];
                body.innerHTML = `
                    <form id="qa-invoice-form">
                        <div class="form-group"><label class="form-label">Customer</label><select id="qa-invoice-customer" name="customer_id" class="form-input"><option value="">Select customer</option>${customers.map(c=>`<option value="${c.id}">${(c.company_name||c.contact_person||c.id)}</option>`).join('')}</select></div>
                        <div class="form-group"><label class="form-label">Related Order (optional)</label><select id="qa-invoice-order" name="order_id" class="form-input"><option value="">None</option></select></div>
                        <div class="form-group"><label class="form-label">Issue Date</label><input name="issue_date" class="form-input" type="date" required /></div>
                        <div class="form-group"><label class="form-label">Due Date</label><input name="due_date" class="form-input" type="date" required /></div>
                        <div class="form-group"><label class="form-label">Subtotal</label><input id="qa-invoice-subtotal" name="subtotal" class="form-input" type="number" step="0.01" required /></div>
                        <div class="form-group"><label class="form-label">Tax Rate (%)</label><input name="tax_rate" class="form-input" type="number" step="0.01" /></div>
                        <div class="form-group"><label class="form-label">Payment Terms</label><input name="payment_terms" class="form-input" required value="Due on receipt" /></div>
                        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-input" rows="3"></textarea></div>
                        <div style="display:flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;"><button type="submit" class="btn btn-primary">Create</button><button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;

                const form = document.getElementById('qa-invoice-form');
                const customerSelect = document.getElementById('qa-invoice-customer');
                const orderSelect = document.getElementById('qa-invoice-order');
                const subtotalInput = document.getElementById('qa-invoice-subtotal');

                async function loadOrdersForCustomer(customerId) {
                    orderSelect.innerHTML = `<option value="">None</option>`;
                    if (!customerId) return;
                    try {
                        const res = await fetch(`/api/orders?customer_id=${customerId}`, {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) return;
                        const json = await res.json();
                        const orders = json.data || [];
                        orders.forEach(o => {
                            const opt = document.createElement('option');
                            opt.value = o.id;
                            opt.textContent = `${o.order_number} — ${o.total_value ?? ''}`;
                            orderSelect.appendChild(opt);
                        });
                    } catch (e) {
                        /* ignore */
                    }
                }

                customerSelect.addEventListener('change', function() {
                    loadOrdersForCustomer(this.value);
                });
                orderSelect.addEventListener('change', async function() {
                    if (!this.value) return;
                    try {
                        const res = await fetch(`/api/orders/${this.value}`, {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) return;
                        const order = await res.json();
                        // If order has total_value, prefill subtotal
                        if (order && order.total_value) subtotalInput.value = order.total_value;
                    } catch (e) {
                        /* ignore */
                    }
                });

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = Object.fromEntries(new FormData(form).entries());
                    submitJson('/api/invoices', data, 'Invoice created');
                });
                return;
            }

            if (type === 'payment') {
                body.innerHTML = `<div>Loading...</div>`;
                // fetch invoices to select from
                const invRes = await fetch('/api/invoices?per_page=1000', {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const invJson = invRes.ok ? await invRes.json().catch(() => null) : null;
                const invList = Array.isArray(invJson) ? invJson : (invJson?.data || []);
                // fetch suggested payment number
                let suggested = '';
                try {
                    const sn = await fetch('/api/payments/suggested-number', {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (sn.ok) {
                        const sj = await sn.json().catch(() => null);
                        suggested = sj?.payment_number || '';
                    }
                } catch (e) {
                    /* ignore */
                }

                body.innerHTML = `
                    <form id="qa-payment-form">
                        <div class="form-group"><label class="form-label">Payment Number</label><input name="payment_number" class="form-input" value="${suggested}" readonly /></div>
                        <div class="form-group"><label class="form-label">Select Invoice</label><select id="qa-payment-invoice" name="invoice_id" class="form-input"><option value="">Choose invoice</option>${(invList||[]).map(i=>`<option data-balance="${i.balance ?? 0}" value="${i.id}">${(i.invoice_number||i.id)} - Balance: ${i.balance ?? 0}</option>`).join('')}</select></div>
                        <div style="display:flex; gap:12px;"><div class="form-group" style="flex:1"><label class="form-label">Payment Date</label><input name="payment_date" type="date" class="form-input" value="${new Date().toISOString().slice(0,10)}" /></div><div class="form-group" style="width:180px"><label class="form-label">Amount (DZD)</label><input name="amount" type="number" step="0.01" class="form-input" value="0.00" /></div></div>
                        <div class="form-group"><label class="form-label">Payment Method</label><select name="method" class="form-input"><option value="bank_transfer">Bank Transfer</option><option value="cash">Cash</option><option value="credit_card">Credit Card</option><option value="wire_transfer">Wire Transfer</option></select></div>
                        <div class="form-group"><label class="form-label">Transaction Reference</label><input name="transaction_reference" class="form-input" placeholder="TXN-XXXXXXXXXX" /></div>
                        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-input" rows="3"></textarea></div>
                        <div style="display:flex; gap: 12px; margin-top: 12px;"><button type="submit" class="btn btn-primary">Create Payment</button><button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;

                const form = document.getElementById('qa-payment-form');
                const invoiceSelect = document.getElementById('qa-payment-invoice');
                const amountInput = form.querySelector('input[name="amount"]');
                invoiceSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const bal = opt ? parseFloat(opt.getAttribute('data-balance') || 0) : 0;
                    if (bal) amountInput.value = Number(bal).toFixed(2);
                });

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const data = Object.fromEntries(new FormData(form).entries());
                    submitJson('/api/payments', data, 'Payment recorded');
                });
                return;
            }

            // fallback: generic name/description form
            body.innerHTML =
                `<div class="form-group"><label class="form-label">Name</label><input type="text" class="form-input" placeholder="Enter name"></div><div class="form-group"><label class="form-label">Description</label><textarea class="form-input" rows="3" placeholder="Enter description"></textarea></div><div style="display:flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;"><button class="btn btn-primary" onclick="submitForm('${type}')">Create</button><button class="btn btn-secondary" onclick="hideModal()">Cancel</button></div>`;
        }
        // expose explicitly on window to avoid being shadowed by page scripts
        window.createItem = createItem;

        // Global Quick Add helper so header Quick Add works on any page
        function showQuickAdd() {
            showModal();
            document.getElementById('modal-title').textContent = 'Quick Add';
            document.getElementById('modal-body').innerHTML = `
                <div style="margin-bottom: 15px;">
                    <input type="text" class="search-input" placeholder="Search actions..." id="quickSearch" onkeyup="filterQuickActions()" style="width: 100%;">
                </div>
                <div id="quickActionsContainer">
                    <h4 style="color: rgb(20, 54, 25); margin: 15px 0 10px 0; font-size: 0.9rem;">Sales & Customer Management</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 15px;">
                        <button class="quick-action-btn" onclick="quickAddItem('customer')" data-keywords="customer client">
                            <i class="uil uil-user-plus" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Customer</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('lead')" data-keywords="lead prospect">
                            <i class="uil uil-users-alt" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Lead</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('quote')" data-keywords="quote quotation">
                            <i class="uil uil-file-edit-alt" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Quote</div>
                        </button>
                    </div>
                    <h4 style="color: rgb(20, 54, 25); margin: 15px 0 10px 0; font-size: 0.9rem;">Orders & Production</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 15px;">
                        <button class="quick-action-btn" onclick="quickAddItem('order')" data-keywords="order">
                            <i class="uil uil-receipt" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Order</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('production')" data-keywords="production">
                            <i class="uil uil-cube" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Production</div>
                        </button>
                    </div>
                    <h4 style="color: rgb(20, 54, 25); margin: 15px 0 10px 0; font-size: 0.9rem;">Inventory & Products</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 15px;">
                        <button class="quick-action-btn" onclick="quickAddItem('product')" data-keywords="product">
                            <i class="uil uil-shopping-bag" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Product</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('material')" data-keywords="material inventory">
                            <i class="uil uil-box" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Material</div>
                        </button>
                    </div>
                    <h4 style="color: rgb(20, 54, 25); margin: 15px 0 10px 0; font-size: 0.9rem;">Finance & Admin</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px;">
                        <button class="quick-action-btn" onclick="quickAddItem('invoice')" data-keywords="invoice bill">
                            <i class="uil uil-invoice" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Invoice</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('payment')" data-keywords="payment">
                            <i class="uil uil-dollar-sign" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Payment</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('task')" data-keywords="task">
                            <i class="uil uil-check-square" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Task</div>
                        </button>
                        <button class="quick-action-btn" onclick="quickAddItem('document')" data-keywords="document file">
                            <i class="uil uil-file-alt" style="font-size: 2rem; margin-bottom: 8px; color: rgb(20, 54, 25);"></i>
                            <div style="font-weight: 600; font-size: 0.85rem;">Document</div>
                        </button>
                    </div>
                </div>
            `;
        }

        function filterQuickActions() {
            const search = document.getElementById('quickSearch') ? document.getElementById('quickSearch').value
                .toLowerCase() : '';
            const buttons = document.querySelectorAll('.quick-action-btn');
            buttons.forEach(btn => {
                const keywords = (btn.getAttribute('data-keywords') || '').toLowerCase();
                btn.style.display = (keywords.includes(search) || search === '') ? 'flex' : 'none';
            });
        }

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
        if (!window.viewOrderDetails) {
            window.viewOrderDetails = async function(orderId) {
                const esc = s => String(s ?? '').replace(/[&<>'"`]/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '\'': '&#39;',
                '"': '&quot;',
                '`': '&#96;'
                } [c] || c));
                try {
                    const res = await fetch('/api/orders/' + encodeURIComponent(orderId), {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Failed to load order');
                    const o = await res.json();
                    const modalTitle = document.getElementById('modal-title');
                    const modalBody = document.getElementById('modal-body');
                    modalTitle.textContent = 'Order ' + (o.order_number || orderId);
                    modalBody.innerHTML = `
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <div><strong>Customer:</strong> ${esc(o.customer?.company_name)}</div>
                            <div><strong>Product:</strong> ${esc(o.product?.name)}</div>
                            <div style="display:flex;gap:12px;"><div><strong>Quantity:</strong> ${esc(o.quantity)} ${esc(o.unit)}</div><div><strong>Total:</strong> DZD ${Number(o.total_value || 0).toLocaleString()}</div></div>
                            <div><strong>Order Date:</strong> ${esc(o.order_date)} <strong>Delivery:</strong> ${esc(o.delivery_date)}</div>
                            <div><strong>Priority:</strong> ${esc(o.priority)}</div>
                            <div><strong>Status:</strong> ${esc(o.status)}</div>
                            <div><strong>Special Instructions:</strong><div style="margin-top:6px;padding:8px;background:#f7fafc;border-radius:6px;">${esc(o.special_instructions)}</div></div>
                        </div>
                    `;
                    if (o.invoice) {
                        modalBody.innerHTML += `
                            <hr />
                            <div><h4>Invoice</h4>
                                <div><strong>Invoice #:</strong> <a href="#" onclick="event.preventDefault(); if (typeof viewInvoice === 'function') { viewInvoice(${o.invoice.id}); } else { return false; }">${esc(o.invoice.invoice_number)}</a></div>
                                <div><strong>Status:</strong> ${esc(o.invoice.status)}</div>
                                <div><strong>Amount:</strong> DZD ${Number(o.invoice.total_amount || o.total_value || 0).toLocaleString()}</div>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML +=
                            `<div style="margin-top:12px;"><button class="btn btn-primary" onclick="createItem('invoice')">Create Invoice</button></div>`;
                    }
                    modalBody.innerHTML +=
                        `<div style="margin-top:12px;"><button class="btn btn-secondary" onclick="hideModal()">Close</button></div>`;
                    showModal();
                } catch (err) {
                    try {
                        showToaster('error', 'Error', 'Failed to load order: ' + (err.message || ''));
                    } catch (e) {
                        console.error(e);
                    }
                }
            };
        }
        // Global helper to view invoice details in a modal. Allows any page (orders module, tracking, etc.)
        if (!window.viewInvoice) {
            window.viewInvoice = async function(invoiceId) {
                const esc = s => String(s ?? '').replace(/[&<>'"`]/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '\'': '&#39;',
                '"': '&quot;',
                '`': '&#96;'
                } [c] || c));
                try {
                    const res = await fetch('/api/invoices/' + encodeURIComponent(invoiceId), {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Failed to load invoice');
                    let inv = await res.json();
                    if (inv && inv.data) inv = inv.data;
                    const modalTitle = document.getElementById('modal-title');
                    const modalBody = document.getElementById('modal-body');
                    modalTitle.textContent = 'Invoice ' + (inv.invoice_number || invoiceId);
                    const payments = Array.isArray(inv.payments) ? inv.payments : (inv.payments?.data || []);
                    modalBody.innerHTML = `
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div>
                                    <div><strong>Customer:</strong> ${esc(inv.customer?.company_name || inv.customer?.contact_person)}</div>
                                    <div><strong>Issue Date:</strong> ${esc(inv.issue_date)}</div>
                                    <div><strong>Due Date:</strong> ${esc(inv.due_date)}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-weight:700; font-size:1.05rem;">Total: DZD ${Number(inv.total_amount || 0).toLocaleString()}</div>
                                    <div style="color:${(inv.balance && Number(inv.balance) > 0) ? '#b04' : '#2a7'}">Balance: DZD ${Number(inv.balance || 0).toLocaleString()}</div>
                                    <div style="margin-top:6px;"><strong>Status:</strong> ${esc(inv.status)}</div>
                                </div>
                            </div>
                            <div>
                                <strong>Notes</strong>
                                <div style="margin-top:6px;padding:10px;background:#f7fafc;border-radius:6px;">${esc(inv.notes)}</div>
                            </div>
                            ${inv.order ? `
                                            <div>
                                                <h4 style="margin:6px 0 4px 0;">Related Order</h4>
                                                <div><strong>Order #:</strong> <a href="#" onclick="event.preventDefault(); if (typeof viewOrderDetails === 'function') { viewOrderDetails(${inv.order.id}); } else { window.location = '/orders/${inv.order.id}'; }">${esc(inv.order.order_number)}</a></div>
                                                <div><strong>Order Status:</strong> ${esc(inv.order.status)}</div>
                                            </div>
                                        ` : ''}
                            <div>
                                <h4 style="margin:6px 0 4px 0;">Payments</h4>
                                <div style="overflow:auto; max-height:240px;">
                                    <table class="table" style="width:100%;">
                                        <thead><tr><th>Date</th><th>Number</th><th>Amount</th><th>Method</th><th>Reference</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            ${payments.map(p => `
                                                            <tr>
                                                                <td>${esc(p.payment_date)}</td>
                                                                <td>${esc(p.payment_number)}</td>
                                                                <td>DZD ${Number(p.amount || 0).toLocaleString()}</td>
                                                                <td>${esc(p.method)}</td>
                                                                <td>${esc(p.transaction_reference)}</td>
                                                                <td><a href="#" onclick="event.preventDefault(); if (typeof viewPayment === 'function') { viewPayment(${p.id}); } else { window.location = '/payments/${p.id}'; }">View</a></td>
                                                            </tr>
                                                        `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                                <button class="btn btn-primary" onclick="(function(){ try{ createItem('payment'); setTimeout(function(){ const sel = document.getElementById('qa-payment-invoice'); if(sel) sel.value='${inv.id}'; const amt = document.querySelector('#qa-payment-form input[name="amount"]'); if(amt) amt.value=Number(${inv.balance || 0}).toFixed(2); },250);}catch(e){} })()">Record Payment</button>
                                <button class="btn btn-secondary" onclick="(function(){ try{ if (typeof sendInvoiceReminder === 'function') { sendInvoiceReminder(${inv.id}); } else { showToaster('info','Reminder','Reminder feature not configured'); } } catch(e){} })()">Send Reminder</button>
                                <button class="btn btn-secondary" onclick="hideModal()">Close</button>
                            </div>
                        </div>
                    `;
                    showModal();
                } catch (err) {
                    try {
                        showToaster('error', 'Error', 'Failed to load invoice: ' + (err.message || ''));
                    } catch (e) {
                        console.error(e);
                    }
                }
            };
        }
        // Global helper to create an invoice pre-filled from an order (order number or id)
        if (!window.createInvoiceFromOrder) {
            window.createInvoiceFromOrder = async function(orderRef) {
                try {
                    // Open quick-add invoice form
                    if (typeof createItem === 'function') {
                        createItem('invoice');
                        // try to fetch order by number or ID and prefill order select
                        setTimeout(async function() {
                            try {
                                const orderI = encodeURIComponent(orderRef);
                                // try lookup by order_number query then by id
                                let res = await fetch('/api/orders?order_number=' + orderI, {
                                    credentials: 'same-origin',
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                                if (res.ok) {
                                    const j = await res.json();
                                    const list = Array.isArray(j) ? j : (j.data || []);
                                    if (list && list.length) {
                                        const order = list[0];
                                        const sel = document.getElementById('qa-invoice-order');
                                        if (sel) {
                                            sel.value = order.id;
                                            const ev = new Event('change');
                                            sel.dispatchEvent(ev);
                                        }
                                        return;
                                    }
                                }
                                // fallback: try GET /api/orders/{id}
                                res = await fetch('/api/orders/' + orderI, {
                                    credentials: 'same-origin',
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                                if (res.ok) {
                                    const order = await res.json();
                                    const id = order?.data ? order.data.id : order.id;
                                    const sel = document.getElementById('qa-invoice-order');
                                    if (sel && id) {
                                        sel.value = id;
                                        sel.dispatchEvent(new Event('change'));
                                    }
                                }
                            } catch (e) {
                                /* ignore prefill errors */
                            }
                        }, 200);
                    } else {
                        // fallback: navigate to invoice page create route
                        window.location = '/invoices/create';
                    }
                } catch (e) {
                    console.error('createInvoiceFromOrder error', e);
                }
            };
        }

        // Compatibility shim: viewInvoiceTracking used in older templates -> prefer viewInvoice
        if (!window.viewInvoiceTracking) {
            window.viewInvoiceTracking = function(invoiceId) {
                if (typeof viewInvoice === 'function') return viewInvoice(invoiceId);
                // fallback: try to open a generic tracking modal used on dashboard
                showModal();
                document.getElementById('modal-title').textContent = 'Payment Tracking - ' + invoiceId;
                document.getElementById('modal-body').innerHTML = `<div style="padding:12px;">Loading...</div>`;
            };
        }

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

    <!-- Minimal safe fallbacks to avoid ReferenceError if other scripts execute before helpers are attached -->
    <script>
        if (!window.showToaster) {
            window.showToaster = function(type, title, message, duration) {
                try {
                    console.log(`TOASTER [${type}] ${title}: ${message}`);
                } catch (e) {}
                // best-effort non-blocking fallback: recreate full toaster DOM structure (icon, title, message, close)
                try {
                    const container = document.getElementById('toaster-container');
                    if (!container) return;
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
                        `<div class="toaster-icon">${icon}</div><div class="toaster-content"><div class="toaster-title">${esc(title)}</div><div class="toaster-message">${esc(message)}</div></div><button class="toaster-close" aria-label="Close" type="button">×</button>`;
                    container.appendChild(t);
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
                        t.classList.remove('show');
                        setTimeout(() => t.remove(), 300);
                    }, duration || 4000);
                } catch (e) {
                    /* ignore */
                }
            };
        }

        if (!window.showConfirm) {
            // fallback tries to use modal overlay with confirm buttons if possible; otherwise native confirm
            window.showConfirm = function(message, title) {
                return new Promise((resolve) => {
                    try {
                        const overlay = document.getElementById('modal-overlay');
                        if (overlay) {
                            const modalTitle = document.getElementById('modal-title');
                            const modalBody = document.getElementById('modal-body');
                            modalTitle.textContent = title || 'Please confirm';
                            overlay.classList.add('show');
                            const yes = document.getElementById('fallback-confirm-yes');
                            const no = document.getElementById('fallback-confirm-no');

                            function cleanup() {
                                try {
                                    if (yes) yes.removeEventListener('click', onYes);
                                    if (no) no.removeEventListener('click', onNo);
                                    overlay.classList.remove('show');
                                } catch (e) {}
                            }

                            function onYes() {
                                cleanup();
                                resolve(true);
                            }

                            function onNo() {
                                cleanup();
                                resolve(false);
                            }
                            if (yes) yes.addEventListener('click', onYes);
                            if (no) no.addEventListener('click', onNo);
                            return;
                        }
                        const result = window.confirm ? window.confirm(message || (title || 'Confirm?')) :
                            false;
                        resolve(Boolean(result));
                    } catch (e) {
                        console.error('showConfirm fallback error', e);
                        resolve(false);
                    }
                });
            };
        }

        // minimal modal show/hide fallback so showModal/hideModal callers don't break
        if (!window.showModal) {
            window.showModal = function() {
                try {
                    const overlay = document.getElementById('modal-overlay');
                    if (overlay) overlay.classList.add('show');
                    else console.warn('modal-overlay not found for showModal fallback');
                } catch (e) {
                    console.error('showModal fallback error', e);
                }
            };
        }
        if (!window.hideModal) {
            window.hideModal = function() {
                try {
                    const overlay = document.getElementById('modal-overlay');
                    if (overlay) {
                        const modalContent = overlay.querySelector('.modal-content');
                        if (modalContent && modalContent.classList.contains('confirm')) modalContent.classList.remove(
                            'confirm');
                        overlay.classList.remove('show');
                    } else console.warn('modal-overlay not found for hideModal fallback');
                } catch (e) {
                    console.error('hideModal fallback error', e);
                }
            };
        }
    </script>

    @stack('scripts')

    @yield('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MZsJ+KJKR8VnUtZbMBqff7vZJf+W9f9PGdG7gqkQMTfNRBpdWOVZ9Is6m3ti+rJ8"
        crossorigin="anonymous"></script>
    <script>
        // Fallback loader: if bootstrap failed to initialize (CDN blocked or integrity mismatch),
        // dynamically load bootstrap bundle from an alternate URL (no integrity) so the page can still use it.
        (function () {
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Toast === 'function') return;
            console.warn('Bootstrap not available (or Toast missing) — attempting dynamic fallback load');

            function loadScript(url, cb) {
                var s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.onload = function () { console.info('Bootstrap fallback loaded:', url); cb(null); };
                s.onerror = function (e) { console.error('Bootstrap fallback failed to load:', url, e); cb(e); };
                document.body.appendChild(s);
            }

            // Try a commonly-available CDN without integrity attribute (helpful during dev)
            loadScript('https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', function (err) {
                if (err) {
                    // final fallback to unpkg
                    loadScript('https://unpkg.com/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', function (err2) {
                        if (err2) console.error('All bootstrap fallback attempts failed');
                    });
                }
            });
        })();
    </script>
</body>

</html>

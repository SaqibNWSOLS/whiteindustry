@extends('layouts.app')

@section('title', 'Workflow')
@section('page_title', 'Workflow')

@section('content')
    <div class="content">
        <div id="workflow" class="module active">
            <div class="tabs">
                <div class="tab-nav">
                    <button class="tab-button active" onclick="showTab('workflow', 'list', this)">List View</button>
                    <button class="tab-button" onclick="showTab('workflow', 'calendar', this)">Calendar View</button>
                </div>
            </div>

            <div id="workflow-list" class="tab-content active">
                    <div class="module-header">
                    <button class="btn btn-primary" onclick="showTaskModal()"><i class="ti ti-plus"></i> Create
                        Task</button>
                    <script>
                        // Early forwarder for submitTaskForm to avoid ReferenceError when inline onclick is used
                        if (typeof window.submitTaskForm !== 'function') {
                            window.__submitTaskQueue = window.__submitTaskQueue || [];
                            window.submitTaskForm = function() {
                                // queue arguments for later consumption by the real handler
                                window.__submitTaskQueue.push(Array.prototype.slice.call(arguments));
                            };
                        }
                    </script>
                    <script>
                        // Ensure a minimal global forwarder exists early so inline onclick won't throw
                        if (typeof window.showTaskModal !== 'function') {
                            window.showTaskModal = function() {
                                const modal = document.getElementById('task-create-modal');
                                if (modal) return modal.classList.add('show');
                                // If modal not present yet, dispatch an event so main script can open it when ready
                                document.dispatchEvent(new Event('openTaskModal'));
                            };
                        }
                    </script>
                    <script>
                        // Provide early forwarders for openEditTask and viewItem so inline onclicks don't throw
                        if (typeof window.openEditTask !== 'function') {
                            window.__openEditTaskQueue = window.__openEditTaskQueue || [];
                            window.openEditTask = function() {
                                window.__openEditTaskQueue.push(Array.prototype.slice.call(arguments));
                            };
                        }
                        if (typeof window.viewItem !== 'function') {
                            window.__viewItemQueue = window.__viewItemQueue || [];
                            window.viewItem = function() {
                                window.__viewItemQueue.push(Array.prototype.slice.call(arguments));
                            };
                        }
                    </script>
                    <input type="search" class="search-input" placeholder="Search tasks...">
                </div>
                <div class="table-container">
                    <div class="table-header">
                        <h3>Task Management</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Title</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tasks-tbody">
                            @foreach(App\Models\Task::orderBy('created_at', 'desc')->limit(50)->get() as $task)
                                <tr data-task-id="{{ $task->id }}">
                                    <td><strong>{{ $task->task_id ?? 'TSK-' . $task->id }}</strong></td>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ optional($task->assignedUser)->full_name ?? optional($task->assignedUser)->name ?? (optional($task)->assigned_to ?? '') }}</td>
                                    <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}</td>
                                    <td><span class="badge {{ $task->priority === 'High' ? 'badge-danger' : ($task->priority === 'Low' ? 'badge-info' : 'badge-warning') }}">{{ $task->priority }}</span></td>
                                    <td><span class="badge badge-info">{{ $task->status }}</span></td>
                                    <td>
                                        <a class="btn btn-secondary js-modal-link" data-modal-title="Task Details" style="padding: 3px 6px; font-size: 0.6rem;" href="{{ route('tasks.show', $task->id) }}">View</a>
                                        <a class="btn btn-primary js-modal-link" data-modal-title="Edit Task" style="padding: 3px 6px; font-size: 0.6rem; margin-left:8px;" href="{{ route('tasks.edit', $task->id) }}">Edit</a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display:inline-block;margin-left:8px;" onsubmit="return confirm('Delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" style="padding: 3px 6px; font-size: 0.6rem;" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Task create modal (inline so scripts are present on this page) -->
            <div id="task-create-modal" class="modal" aria-hidden="true">
                <div class="modal-content modal-content--small">
                    <div class="modal-header">
                        <h3>Create Task</h3>
                        <button class="close" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="task-create-form" action="{{ route('tasks.store') }}" method="POST">
                            @csrf
                            <div class="form-group"><label class="form-label">Task ID</label><input type="text" class="form-input" id="task-id" readonly></div>
                            <div class="form-group"><label class="form-label">Task Title</label><input type="text" name="title" value="{{ old('title') }}" class="form-input" id="task-title" placeholder="Task title" required></div>
                            @error('title')<div class="field-error">{{ $message }}</div>@enderror
                            <div class="form-group"><label class="form-label">Description</label><textarea name="description" id="task-desc" class="form-input" rows="3" placeholder="Task description">{{ old('description') }}</textarea></div>
                            @error('description')<div class="field-error">{{ $message }}</div>@enderror
                            @php
                                // Show only internal users (exclude those with the 'customer' role)
                                $assignableUsers = App\Models\User::whereDoesntHave('roles', function($q){ $q->where('name','customer'); })->orderBy('first_name')->take(200)->get();
                            @endphp
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                <div class="form-group"><label class="form-label">Assigned To</label><select name="assigned_to" id="task-assigned" class="form-select"><option value="">Select user</option>@foreach($assignableUsers as $u)<option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->full_name }}</option>@endforeach</select></div>
                                @error('assigned_to')<div class="field-error">{{ $message }}</div>@enderror
                                <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" id="task-due" value="{{ old('due_date') }}" class="form-input"></div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:12px;">
                                <div class="form-group"><label class="form-label">Priority</label><select name="priority" id="task-priority" class="form-select"><option value="low" {{ old('priority')=='low' ? 'selected' : '' }}>Low</option><option value="normal" {{ old('priority')=='' || old('priority')=='normal' ? 'selected' : '' }}>Normal</option><option value="high" {{ old('priority')=='high' ? 'selected' : '' }}>High</option><option value="urgent" {{ old('priority')=='urgent' ? 'selected' : '' }}>Urgent</option></select></div>
                                @error('priority')<div class="field-error">{{ $message }}</div>@enderror
                                <div class="form-group"><label class="form-label">Status</label><select name="status" id="task-status" class="form-select"><option value="not_started" {{ old('status')=='not_started' ? 'selected' : '' }}>Not Started</option><option value="in_progress" {{ old('status')=='' || old('status')=='in_progress' ? 'selected' : '' }}>In Progress</option><option value="completed" {{ old('status')=='completed' ? 'selected' : '' }}>Completed</option></select></div>
                                @error('status')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group" style="margin-top:12px;"><label class="form-label">Related To</label><select name="related" id="task-related" class="form-select"><option value="">Select related item</option><option {{ old('related')=='ORD-2024-001 - Sales Order' ? 'selected' : '' }}>ORD-2024-001 - Sales Order</option><option {{ old('related')=='OF-2024-0156 - Production Order' ? 'selected' : '' }}>OF-2024-0156 - Production Order</option><option {{ old('related')=='QC Batch #1234' ? 'selected' : '' }}>QC Batch #1234</option></select></div>
                            @error('related')<div class="field-error">{{ $message }}</div>@enderror
                            <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:18px;"><button type="submit" class="btn btn-primary">Create Task</button><button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="workflow-calendar" class="tab-content">
                <div class="module-header">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button class="btn btn-secondary" onclick="changeMonth(-1)">
                            <i class="ti ti-chevron-left"></i> Previous
                        </button>
                        <h3 id="calendar-month-year" style="margin: 0; min-width: 180px; text-align: center;">September 2024
                        </h3>
                        <button class="btn btn-secondary" onclick="changeMonth(1)">
                            Next <i class="ti ti-chevron-right"></i>
                        </button>
                        <button class="btn btn-secondary" onclick="goToToday()">
                            <i class="ti ti-calendar-event"></i> Today
                        </button>
                    </div>
                        <button class="btn btn-primary" onclick="showTaskModal()"><i class="ti ti-plus"></i> Create
                        Task</button>
                        <script>
                            // Ensure forwarder for calendar button too
                            if (typeof window.showTaskModal !== 'function') {
                                window.showTaskModal = function() {
                                    const modal = document.getElementById('task-create-modal');
                                    if (modal) return modal.classList.add('show');
                                    document.dispatchEvent(new Event('openTaskModal'));
                                };
                            }
                        </script>
                </div>

                <div style="background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border: 1px solid #e5e5e5;">
                    <!-- FullCalendar placeholder -->
                    <div id="workflow-fullcalendar"></div>

                    <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; display: flex; gap: 20px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 20px; height: 20px; background: #ef4444; border-radius: 3px;"></div>
                            <span style="font-size: 0.8rem;">High Priority</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 20px; height: 20px; background: #f59e0b; border-radius: 3px;"></div>
                            <span style="font-size: 0.8rem;">Normal Priority</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 20px; height: 20px; background: #3b82f6; border-radius: 3px;"></div>
                            <span style="font-size: 0.8rem;">Low Priority</span>
                        </div>
                    </div>
                    <!-- Calendar is initialized from the bundled app JS (Vite). -->
                </div>
            </div>
        </div>

    </div>
@endsection
    <!-- Central modal overlay used by inventory and other modules. Added for workflow view/edit modals -->
    <div id="modal-overlay" class="modal-overlay" aria-hidden="true" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Modal</h3>
                <button class="close" type="button" onclick="hideModal()">&times;</button>
            </div>
            <div id="modal-body" class="modal-body" style="padding:12px;"></div>
        </div>
    </div>
    <style>
        /* Minimal modal-overlay styles used by inventory module (kept local to workflow to avoid global style breaks) */
        #modal-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); z-index: 2000; }
        #modal-overlay.show { display: flex; }
        #modal-overlay .modal-content { background: #fff; border-radius: 8px; max-width: 720px; width: 96%; box-shadow: 0 6px 24px rgba(0,0,0,0.2); }
        #modal-overlay .modal-header { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid #eee; }
        #modal-overlay .modal-body { padding:16px; max-height: 70vh; overflow:auto; }
    </style>
@section('scripts')
<script>
            function showToaster(type, title, message, duration = 4000) {
            const container = document.getElementById('toaster-container');
            const toaster = document.createElement('div');
            toaster.className = `toaster ${type}`;
            
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };

            toaster.innerHTML = `
                <div class="toaster-icon">${icons[type]}</div>
                <div class="toaster-content">
                    <div class="toaster-title">${title}</div>
                    <div class="toaster-message">${message}</div>
                </div>
                <button class="toaster-close" onclick="removeToaster(this.parentElement)">×</button>
            `;

            container.appendChild(toaster);
            setTimeout(() => toaster.classList.add('show'), 100);
            setTimeout(() => removeToaster(toaster), duration);
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

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

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
            document.querySelectorAll(`#${moduleId} .tab-content`).forEach(content => {
                content.classList.remove('active');
            });

            const targetTab = document.getElementById(`${moduleId}-${tabId}`);
            if (targetTab) {
                targetTab.classList.add('active');
            }

            document.querySelectorAll(`#${moduleId} .tab-button`).forEach(button => {
                button.classList.remove('active');
            });

            if (clickedElement) {
                clickedElement.classList.add('active');
            }
        }

        // --- Task modal helpers (provide global implementations to avoid ReferenceError) ---
        function showTaskModal() {
            const modal = document.getElementById('task-create-modal');
            const form = document.getElementById('task-create-form');
            if (!modal || !form) return;
            // Reset form to create mode
            form.reset();
            // remove any _method input used for edit
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.parentNode.removeChild(methodInput);
            // ensure action points to create route (blade rendered action remains correct)
            // show readable task id placeholder
            const taskIdInput = document.getElementById('task-id');
            if (taskIdInput) taskIdInput.value = 'Auto-generated';
            // change submit button text
            const submit = form.querySelector('button[type="submit"]');
            if (submit) submit.textContent = 'Create Task';
            modal.classList.add('show');
            // also trigger global overlay if available for consistent UX
            if (typeof showModal === 'function') try { showModal(); } catch (e) {}
        }

        function hideTaskModal() {
            const modal = document.getElementById('task-create-modal');
            if (!modal) return;
            modal.classList.remove('show');
            if (typeof hideModal === 'function') try { hideModal(); } catch (e) {}
            const overlay = document.getElementById('modal-overlay'); if (overlay) overlay.classList.remove('show');
        }

        // Fetch task data and populate the create form for editing
        async function openEditTask(id) {
            try {
                const res = await fetch(`/tasks/${id}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) {
                    const txt = await res.text();
                    showToaster('error', 'Error', 'Failed to load task: ' + (txt || res.status));
                    return;
                }
                const payload = await res.json();
                const task = payload.data || payload;
                const form = document.getElementById('task-create-form');
                if (!form) return;
                // ensure _method is present for PUT/PATCH
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                // set form action to update endpoint
                form.action = `/tasks/${task.id}`;
                // populate fields
                document.getElementById('task-title').value = task.title || '';
                document.getElementById('task-desc').value = task.description || '';
                const assignedSelect = document.getElementById('task-assigned');
                if (assignedSelect) assignedSelect.value = task.assigned_to || '';
                document.getElementById('task-due').value = task.due_date ? task.due_date.split('T')[0] : '';
                const pr = document.getElementById('task-priority'); if (pr) pr.value = task.priority || 'Normal';
                const st = document.getElementById('task-status'); if (st) st.value = task.status || 'Not Started';
                const rel = document.getElementById('task-related'); if (rel) rel.value = task.related || '';
                const taskIdInput = document.getElementById('task-id'); if (taskIdInput) taskIdInput.value = task.task_id || ('TSK-' + task.id);
                const submit = form.querySelector('button[type="submit"]'); if (submit) submit.textContent = 'Update Task';
                // show modal
                const modal = document.getElementById('task-create-modal'); if (modal) modal.classList.add('show');
            } catch (e) {
                console.error('openEditTask error', e);
                showToaster('error', 'Error', 'Failed to load task');
            }
        }

        // View item: try to map task id string (e.g., TSK-001) to a numeric row, else attempt openEditTask
        function viewItem(identifier) {
            // If numeric, open by id
            const tryOpen = async (id) => {
                try {
                    const res = await fetch(`/tasks/${id}`, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Failed to load');
                    const payload = await res.json(); const task = payload.data || payload;
                    // Use central modal overlay if available
                    let overlay = document.getElementById('modal-overlay');
                    if (!overlay) {
                        // fallback: reuse task-create-modal in read-only mode
                        const form = document.getElementById('task-create-form');
                        if (form) {
                            showTaskModal();
                            // populate but disable inputs
                            document.getElementById('task-title').value = task.title || '';
                            document.getElementById('task-desc').value = task.description || '';
                            const pr = document.getElementById('task-priority'); if (pr) pr.value = task.priority || '';
                            const st = document.getElementById('task-status'); if (st) st.value = task.status || '';
                            // disable inputs to make it view-only
                            Array.from(form.querySelectorAll('input,textarea,select,button[type="submit"]')).forEach(el => el.disabled = true);
                            return;
                        }
                    }
                    // show overlay and populate
                    if (!overlay) return;
                    overlay.classList.add('show');
                    const titleEl = overlay.querySelector('#modal-title');
                    const bodyEl = overlay.querySelector('#modal-body');
                    if (titleEl) titleEl.textContent = `Task ${task.task_id || task.id}`;
                    if (bodyEl) bodyEl.innerHTML = `
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <div><strong>${escapeHtml(task.title||'')}</strong></div>
                            <div>${escapeHtml(task.description||'')}</div>
                            <div>Assigned: ${escapeHtml(task.assigned_user?.full_name || task.assigned_to || '')}</div>
                            <div>Due: ${task.due_date ? (task.due_date.split('T')[0]) : ''}</div>
                            <div>Priority: ${escapeHtml(task.priority||'')}</div>
                            <div>Status: ${escapeHtml(task.status||'')}</div>
                            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" onclick="hideModal();">Close</button></div>
                        </div>
                    `;
                } catch (e) {
                    console.error('viewItem error', e);
                    showToaster('error','Error','Failed to load task');
                }
            };

            if (/^\d+$/.test(String(identifier))) return tryOpen(parseInt(identifier, 10));
            const rows = document.querySelectorAll('#tasks-tbody tr');
            for (const tr of rows) {
                const strong = tr.querySelector('td strong');
                if (strong && strong.textContent.trim() === String(identifier)) {
                    const numeric = tr.getAttribute('data-task-id');
                    if (numeric) return tryOpen(numeric);
                }
            }
            // fallback: try to open by treating identifier as numeric id
            tryOpen(identifier);
        }

        async function deleteTask(id) {
            if (!confirm('Delete this task?')) return;
            try {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const headers = { 'Accept': 'application/json' };
                if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
                const res = await fetch(`/tasks/${id}`, { method: 'DELETE', headers });
                if (!res.ok) {
                    const txt = await res.text();
                    showToaster('error', 'Error', 'Delete failed: ' + (txt || res.status));
                    return;
                }
                // remove row from table
                const row = document.querySelector(`#tasks-tbody tr[data-task-id=\"${id}\"]`);
                if (row && row.parentNode) row.parentNode.removeChild(row);
                showToaster('success', 'Deleted', 'Task deleted');
            } catch (e) {
                console.error('deleteTask error', e);
                showToaster('error', 'Error', 'Failed to delete task');
            }
        }

        // Expose to global so inline onclicks won't throw (overrides earlier forwarders)
        window.showTaskModal = showTaskModal;
        window.hideTaskModal = hideTaskModal;
        window.openEditTask = openEditTask;
        window.viewItem = viewItem;
        window.deleteTask = deleteTask;

        // Delegate button clicks (view/edit/delete) to avoid inline onclick usage and ReferenceError
        (function attachTaskActionHandler() {
            const handler = function (e) {
                try {
                    // use Element.closest if available, otherwise walk up the tree
                    let el = e.target;
                    if (el.closest) el = el.closest('button[data-action]');
                    else {
                        while (el && el !== document.body) {
                            if (el.tagName && el.tagName.toLowerCase() === 'button' && el.hasAttribute && el.hasAttribute('data-action')) break;
                            el = el.parentNode;
                        }
                    }
                    const btn = el;
                    if (!btn) return;
                    const action = btn.getAttribute('data-action');
                    const id = btn.getAttribute('data-id');
                    if (!action || !id) return;
                    // dispatch to handlers
                    if (action === 'view') {
                        viewItem(id);
                    } else if (action === 'edit') {
                        openEditTask(Number(id));
                    } else if (action === 'delete') {
                        deleteTask(Number(id));
                    }
                } catch (err) {
                    console.error('task action handler error', err);
                }
            };

            const attachTo = document.getElementById('tasks-tbody') || document;
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { attachTo.addEventListener('click', handler); });
            } else {
                // DOM already ready — attach immediately
                attachTo.addEventListener('click', handler);
            }
        })();

        // Intercept clicks on modal links inside the table to show content in central modal
        (function attachModalLinkHandler(){
            const table = document.getElementById('tasks-tbody');
            const clickHandler = function(e){
                const a = e.target.closest && e.target.closest('a.js-modal-link');
                if (!a) return;
                e.preventDefault();
                const href = a.getAttribute('href');
                const title = a.getAttribute('data-modal-title') || '';
                if (href) loadIntoModal(href, title);
            };
            if (table) table.addEventListener('click', clickHandler);
            else document.addEventListener('click', clickHandler);
        })();


        function createItem(type) {
            showModal();
            document.getElementById('modal-title').textContent = `Create New ${formatTitle(type)}`;
            
            const forms = {
                'customer': `
                    <div class="form-group">
                        <label class="form-label">Customer Type</label>
                        <select class="form-select" id="customerType" onchange="toggleCustomerFields()">
                            <option value="">Select Type</option>
                            <option value="person">Person</option>
                            <option value="business">Business</option>
                        </select>
                    </div>
                    <div id="businessFields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-input" placeholder="Enter company name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Industry Type</label>
                            <select class="form-select">
                                <option value="">Select industry</option>
                                <option>Cosmetics & Beauty</option>
                                <option>Pharmaceuticals</option>
                                <option>Dietary Supplements</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tax ID</label>
                            <input type="text" class="form-input" placeholder="DZ-XXXXXXXXX">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-input" placeholder="Full name">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-input" placeholder="+213 XXX XXX XXX">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea class="form-input" rows="2" placeholder="Full address"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="form-input" placeholder="City">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-input" placeholder="Postal code">
                        </div>
                    </div>
                `,
                'lead': `
                    <div class="form-group">
                        <label class="form-label">Lead Source</label>
                        <select class="form-select">
                            <option value="">Select source</option>
                            <option>Website</option>
                            <option>Referral</option>
                            <option>Trade Show</option>
                            <option>Cold Call</option>
                            <option>Social Media</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company Name</label>
                        <input type="text" class="form-input" placeholder="Company name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-input" placeholder="Full name">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-input" placeholder="+213 XXX XXX XXX">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Lead Status</label>
                            <select class="form-select">
                                <option>New</option>
                                <option>Contacted</option>
                                <option>Qualified</option>
                                <option>Proposal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estimated Value (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-input" rows="3" placeholder="Additional notes"></textarea>
                    </div>
                `,
                'quote': `
                    <div class="form-group">
                        <label class="form-label">Quote Number</label>
                        <input type="text" class="form-input" value="QUO-2024-${Math.floor(Math.random()*1000)}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer</label>
                        <select class="form-select">
                            <option value="">Select customer</option>
                            <option>Cosmetic Solutions Ltd.</option>
                            <option>PharmaTech Industries</option>
                            <option>Beauty Global Corp</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product/Service</label>
                        <select class="form-select">
                            <option value="">Select product</option>
                            <option>Face Cream Base</option>
                            <option>Supplement Capsules</option>
                            <option>Body Lotion</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-input" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unit</label>
                            <select class="form-select">
                                <option>L</option>
                                <option>kg</option>
                                <option>units</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Unit Price (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Amount (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00" readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Valid Until</label>
                            <input type="date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Payment Terms</label>
                            <select class="form-select">
                                <option>Net 30</option>
                                <option>Net 60</option>
                                <option>50% Upfront</option>
                                <option>Cash on Delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-input" rows="2" placeholder="Additional terms or notes"></textarea>
                    </div>
                `,
                'order': `
                    <div class="form-group">
                        <label class="form-label">Order Number</label>
                        <input type="text" class="form-input" value="ORD-2024-${Math.floor(Math.random()*1000)}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer</label>
                        <select class="form-select">
                            <option value="">Select customer</option>
                            <option>Cosmetic Solutions Ltd.</option>
                            <option>PharmaTech Industries</option>
                            <option>Beauty Global Corp</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product</label>
                        <select class="form-select">
                            <option value="">Select product</option>
                            <option>Face Cream Base</option>
                            <option>Anti-Aging Cream</option>
                            <option>Supplement Capsules</option>
                            <option>Body Lotion</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-input" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unit</label>
                            <select class="form-select">
                                <option>L</option>
                                <option>kg</option>
                                <option>units</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Value (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Order Date</label>
                            <input type="date" class="form-input" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Delivery Date</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select class="form-select">
                            <option>Normal</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Special Instructions</label>
                        <textarea class="form-input" rows="3" placeholder="Any special requirements"></textarea>
                    </div>
                `,
                'production': `
                    <div class="form-group">
                        <label class="form-label">Production Order (OF) Number</label>
                        <input type="text" class="form-input" value="OF-2024-${Math.floor(Math.random()*10000)}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Related Sales Order</label>
                        <select class="form-select">
                            <option value="">Select order</option>
                            <option>ORD-2024-001 - Face Cream Base</option>
                            <option>ORD-2024-002 - Supplement Capsules</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product to Manufacture</label>
                        <select class="form-select">
                            <option value="">Select product</option>
                            <option>Face Cream Base</option>
                            <option>Anti-Aging Cream</option>
                            <option>Supplement Capsules</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Quantity to Produce</label>
                            <input type="number" class="form-input" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unit</label>
                            <select class="form-select">
                                <option>L</option>
                                <option>kg</option>
                                <option>units</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-input" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Production Line</label>
                        <select class="form-select">
                            <option>Line A - Creams</option>
                            <option>Line B - Supplements</option>
                            <option>Line C - Lotions</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-input" placeholder="BATCH-YYYYMMDD-XXX">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-input" rows="2" placeholder="Production notes"></textarea>
                    </div>
                `,
                'product': `
                    <div class="form-group">
                        <label class="form-label">Product Category</label>
                        <select class="form-select" id="productCategory">
                            <option value="">Select category</option>
                            <option value="raw">Raw Material</option>
                            <option value="packaging">Packaging</option>
                            <option value="final">Final Product</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product Code</label>
                        <input type="text" class="form-input" placeholder="PRD-XXX-001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-input" placeholder="Product name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-input" rows="2" placeholder="Product description"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Unit Price (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unit of Measure</label>
                            <select class="form-select">
                                <option>L</option>
                                <option>kg</option>
                                <option>pc</option>
                                <option>unit</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product Type</label>
                        <select class="form-select">
                            <option>Face Care</option>
                            <option>Body Care</option>
                            <option>Supplements</option>
                            <option>Active Ingredient</option>
                            <option>Base Material</option>
                            <option>Container</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option>Active</option>
                            <option>Inactive</option>
                            <option>Discontinued</option>
                        </select>
                    </div>
                `,
                'material': `
                    <div class="form-group">
                        <label class="form-label">Material Type</label>
                        <select class="form-select" id="materialType" onchange="toggleMaterialFields()">
                            <option value="">Select Type</option>
                            <option value="raw">Raw Material</option>
                            <option value="packaging">Packaging</option>
                            <option value="final">Final Product</option>
                        </select>
                    </div>
                    <div id="materialFields" style="display:none;">
                        <div class="form-group">
                            <label class="form-label">Material Code</label>
                            <input type="text" class="form-input" placeholder="MP-XXX-001">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Material Name</label>
                            <input type="text" class="form-input" placeholder="Material name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select class="form-select">
                                <option>Active Ingredient</option>
                                <option>Base Material</option>
                                <option>Preservative</option>
                                <option>Fragrance</option>
                                <option>Container</option>
                                <option>Accessory</option>
                            </select>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Current Stock</label>
                                <input type="number" class="form-input" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Minimum Stock Level</label>
                                <input type="number" class="form-input" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Unit</label>
                                <select class="form-select">
                                    <option>kg</option>
                                    <option>L</option>
                                    <option>pc</option>
                                    <option>unit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Unit Cost (DZD)</label>
                                <input type="number" class="form-input" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-input" placeholder="Supplier name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Storage Location</label>
                            <input type="text" class="form-input" placeholder="Warehouse location">
                        </div>
                    </div>
                `,
                'invoice': `
                    <div class="form-group">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-input" value="INV-2024-${Math.floor(Math.random()*1000)}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer</label>
                        <select class="form-select">
                            <option value="">Select customer</option>
                            <option>Cosmetic Solutions Ltd.</option>
                            <option>PharmaTech Industries</option>
                            <option>Beauty Global Corp</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Related Order</label>
                        <select class="form-select">
                            <option value="">Select order</option>
                            <option>ORD-2024-001</option>
                            <option>ORD-2024-002</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" class="form-input" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Subtotal (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="19" value="19">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Amount (DZD)</label>
                        <input type="number" class="form-input" step="0.01" placeholder="0.00" readonly style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Terms</label>
                        <select class="form-select">
                            <option>Net 30</option>
                            <option>Net 60</option>
                            <option>Due on Receipt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-input" rows="2" placeholder="Additional notes"></textarea>
                    </div>
                `,
                'payment': `
                    <div class="form-group">
                        <label class="form-label">Payment Number</label>
                        <input type="text" class="form-input" value="PAY-2024-${Math.floor(Math.random()*1000)}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Select Invoice</label>
                        <select class="form-select">
                            <option value="">Choose invoice</option>
                            <option>INV-2024-0156 - DZD 18,750 (Outstanding)</option>
                            <option>INV-2024-0142 - DZD 12,890 (Overdue)</option>
                            <option>INV-2024-0138 - DZD 8,450 (Partial)</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-input" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Amount (DZD)</label>
                            <input type="number" class="form-input" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select">
                            <option>Bank Transfer</option>
                            <option>Credit Card</option>
                            <option>Check</option>
                            <option>Cash</option>
                            <option>Wire Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" class="form-input" placeholder="TXN-XXXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-input" rows="2" placeholder="Payment notes"></textarea>
                    </div>
                `,
                'task': `
                    <div class="form-group">
                        <label class="form-label">Task ID</label>
                        <input type="text" class="form-input" value="TSK-${Math.floor(Math.random()*1000)}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Task Title</label>
                        <input type="text" class="form-input" placeholder="Task title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-input" rows="3" placeholder="Task description"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Assigned To</label>
                            <select class="form-select">
                                <option value="">Select user</option>
                                <option>Pierre Blanc</option>
                                <option>Marie Dubois</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Priority</label>
                            <select class="form-select">
                                <option>Low</option>
                                <option>Normal</option>
                                <option>High</option>
                                <option>Urgent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option>Not Started</option>
                                <option>In Progress</option>
                                <option>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Related To</label>
                        <select class="form-select">
                            <option value="">Select related item</option>
                            <option>ORD-2024-001 - Sales Order</option>
                            <option>OF-2024-0156 - Production Order</option>
                            <option>QC Batch #1234</option>
                        </select>
                    </div>
                `,
                'document': `
                    <div class="form-group">
                        <label class="form-label">Document Name</label>
                        <input type="text" class="form-input" placeholder="Document name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Document Type</label>
                        <select class="form-select">
                            <option>SOP</option>
                            <option>Certificate</option>
                            <option>Contract</option>
                            <option>Report</option>
                            <option>Invoice</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Upload File</label>
                        <input type="file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Version</label>
                            <input type="text" class="form-input" placeholder="v1.0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Related To</label>
                        <select class="form-select">
                            <option value="">Select related item</option>
                            <option>Customer - Cosmetic Solutions Ltd.</option>
                            <option>Product - Face Cream Base</option>
                            <option>Order - ORD-2024-001</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-input" rows="2" placeholder="Document description"></textarea>
                    </div>
                `,
                'user': `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-input" placeholder="First name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-input" placeholder="Last name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" placeholder="email@whiteindustry.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-input" placeholder="+213 XXX XXX XXX">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Job Title</label>
                            <input type="text" class="form-input" placeholder="Job title">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Department</label>
                            <select class="form-select">
                                <option>Production</option>
                                <option>Quality Control</option>
                                <option>Sales</option>
                                <option>Administration</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">User Role</label>
                            <select class="form-select">
                                <option>User</option>
                                <option>Manager</option>
                                <option>Administrator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Initial Password</label>
                        <input type="password" class="form-input" placeholder="Temporary password">
                    </div>
                `
            };

            const defaultForm = `
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-input" placeholder="Enter name">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-input" rows="3" placeholder="Enter description"></textarea>
                </div>
            `;

            document.getElementById('modal-body').innerHTML = `
                ${forms[type] || defaultForm}
                <div style="display: flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;">
                    <button class="btn btn-primary" onclick="submitForm('${type}')">Create ${formatTitle(type)}</button>
                    <button class="btn btn-secondary" onclick="hideModal()">Cancel</button>
                </div>
            `;
        }



        function filterQuickActions() {
            const search = document.getElementById('quickSearch').value.toLowerCase();
            const buttons = document.querySelectorAll('.quick-action-btn');
            buttons.forEach(btn => {
                const keywords = btn.getAttribute('data-keywords') || '';
                btn.style.display = keywords.includes(search) || search === '' ? 'flex' : 'none';
            });
        }

        function quickAddItem(type) {
            hideModal();
            setTimeout(() => createItem(type), 100);
        }

        function showModal() {
            document.getElementById('modal-overlay').classList.add('show');
        }

        function hideModal() {
            document.getElementById('modal-overlay').classList.remove('show');
        }

        // Load remote page (show/edit) into central modal. The fetched page may be a full layout; try to extract a useful fragment (form or main/content)
        async function loadIntoModal(url, title) {
            try {
                const bodyEl = document.getElementById('modal-body');
                const titleEl = document.getElementById('modal-title');
                if (titleEl) titleEl.textContent = title || '';
                if (bodyEl) bodyEl.innerHTML = '<div style="padding:12px">Loading...</div>';
                const res = await fetch(url, { credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest','Accept':'text/html'} });
                if (!res.ok) {
                    const txt = await res.text().catch(()=>null);
                    if (bodyEl) bodyEl.innerHTML = '<div style="padding:12px">Failed to load content</div>';
                    return;
                }
                const text = await res.text();
                // Parse HTML and try to extract a form or main/content element
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                let fragment = null;
                // prefer a form with id task-create-form or any form
                fragment = doc.querySelector('#task-create-form') || doc.querySelector('form');
                if (!fragment) fragment = doc.querySelector('main') || doc.querySelector('.content') || doc.querySelector('.modal-body');
                if (fragment) {
                    // Only inject the innerHTML of the fragment to avoid bringing in <html>/<body> tags
                    if (bodyEl) bodyEl.innerHTML = fragment.innerHTML;
                } else {
                    // If the response is a full page, try to extract the body content only
                    const docBody = doc.body;
                    if (docBody && bodyEl) bodyEl.innerHTML = docBody.innerHTML;
                    else if (bodyEl) bodyEl.innerHTML = text;
                }
                // show modal
                showModal();
            } catch (err) {
                console.error('loadIntoModal error', err);
                const bodyEl = document.getElementById('modal-body');
                if (bodyEl) bodyEl.innerHTML = '<div style="padding:12px">Error loading content</div>';
                showModal();
            }
        }

        function formatTitle(type) {
            return type.split('-').map(word => 
                word.charAt(0).toUpperCase() + word.slice(1)
            ).join(' ');
        }

        function submitForm(type) {
            hideModal();
            showToaster('success', 'Success!', `${formatTitle(type)} created successfully!`);
        }

        /* Task modal for Workflow: show/hide and submit handlers */
    window.showTaskModal = function() {
            // create modal HTML if not present
            if (!document.getElementById('task-create-modal')) {
                const modal = document.createElement('div');
                modal.id = 'task-create-modal';
                modal.className = 'modal-overlay';
                modal.innerHTML = `
                    <div class="modal" style="max-width:640px;">
                        <div class="modal-header"><h3>Create Task</h3></div>
                        <div class="modal-body">
                            <form id="task-create-form">
                                <div class="form-group">
                                    <label class="form-label">Task ID</label>
                                    <input type="text" class="form-input" id="task-id" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Task Title</label>
                                    <input type="text" class="form-input" id="task-title" placeholder="Task title">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-input" id="task-desc" rows="3" placeholder="Task description"></textarea>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="form-group">
                                        <label class="form-label">Assigned To</label>
                                        <select class="form-select" id="task-assigned">
                                            <option value="">Select user</option>
                                            <option>Pierre Blanc</option>
                                            <option>Marie Dubois</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" class="form-input" id="task-due">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top:10px;">
                                    <div class="form-group">
                                        <label class="form-label">Priority</label>
                                        <select class="form-select" id="task-priority">
                                            <option>Low</option>
                                            <option selected>Normal</option>
                                            <option>High</option>
                                            <option>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" id="task-status">
                                            <option>Not Started</option>
                                            <option selected>In Progress</option>
                                            <option>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top:12px;">
                                    <label class="form-label">Related To</label>
                                    <select class="form-select" id="task-related">
                                        <option value="">Select related item</option>
                                        <option>ORD-2024-001 - Sales Order</option>
                                        <option>OF-2024-0156 - Production Order</option>
                                        <option>QC Batch #1234</option>
                                    </select>
                                </div>
                                <div style="display:flex; gap:12px; margin-top:18px; justify-content:flex-end;">
                                    <button type="button" class="btn btn-primary" onclick="submitTaskForm()">Create Task</button>
                                    <button type="button" class="btn btn-secondary" onclick="hideTaskModal()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            // clear form for new task
            const form = document.getElementById('task-create-form');
            if (form) {
                form.removeAttribute('data-editing');
                try { form.reset(); } catch(e) {}
            }
            const idEl = document.getElementById('task-id');
            if (idEl) idEl.value = 'Auto-generated';
            document.getElementById('task-create-modal').classList.add('show');
        }

        window.hideTaskModal = function() {
            // Remove visible markers from any task-create modal variant and the central overlay
            try {
                // If a static modal exists with id
                const modal = document.getElementById('task-create-modal');
                if (modal) modal.classList.remove('show');

                // If a dynamically created modal overlay (or other modal wrappers) exists, hide it too
                document.querySelectorAll('#task-create-modal, .modal-overlay, .modal').forEach(el => {
                    if (el && el.classList) el.classList.remove('show');
                });

                const overlay = document.getElementById('modal-overlay');
                if (overlay) overlay.classList.remove('show');
            } catch (e) {
                console.error('hideTaskModal error', e);
            }
        }

    window.submitTaskForm = function() {
        console.log('[Workflow] submitTaskForm called');
        const title = document.getElementById('task-title')?.value || '';
            // simple validation
            if (!title.trim()) {
                showToaster('error', 'Missing title', 'Please enter a task title');
                return;
            }

            const payload = {
                title: title.trim(),
                description: document.getElementById('task-desc')?.value || null,
                assigned_to: document.getElementById('task-assigned')?.value || null,
                due_date: document.getElementById('task-due')?.value || null,
                priority: document.getElementById('task-priority')?.value || 'Normal',
                status: document.getElementById('task-status')?.value || 'Not Started',
                related: document.getElementById('task-related')?.value || null,
            };

            // disable submit button while saving
            const form = document.getElementById('task-create-form');
            const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.btn-primary');
            if (submitBtn) submitBtn.disabled = true;

            // detect edit mode
            const editingId = form.getAttribute('data-editing') || '';
            const isEdit = !!editingId;
            const url = isEdit ? `/tasks/${editingId}` : '/tasks';
            const method = isEdit ? 'PUT' : 'POST';

            // clear previous inline errors
            clearFormErrors();

            console.log('[Workflow] sending', method, url, payload);
            fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            }).then(async res => {
                let data = null;
                try { data = await res.json(); } catch(e) { /* ignore parse errors */ }
                console.log('[Workflow] response', res.status, data);
                if (!res.ok) {
                    // 419 - session expired
                    if (res.status === 419) {
                        throw new Error('Session expired. Please reload and log in again.');
                    }
                    // handle validation errors (Laravel returns 422 with { errors: { field: [..] } })
                    if (data && data.errors) {
                        showFormErrors(data.errors);
                    }
                    const msg = (data && (data.message || data.error || data.errors)) ? (data.message || 'Validation error') : 'Failed to save task';
                    throw new Error(msg);
                }
                return data;
            }).then(data => {
                if (data && data.success) {
                    hideTaskModal();
                    showToaster('success', isEdit ? 'Task updated' : 'Task created', `${data.data.task_id || data.data.id} — ${data.data.title}`);

                    // Update or insert row in the task table
                    try {
                        const tbody = document.querySelector('#tasks-tbody');
                        if (tbody) {
                            if (isEdit) {
                                const existing = tbody.querySelector(`tr[data-task-id="${editingId}"]`);
                                if (existing) {
                                    existing.innerHTML = `
                                        <td><strong>${escapeHtml(data.data.task_id || 'TSK-' + (data.data.id || ''))}</strong></td>
                                        <td>${escapeHtml(data.data.title)}</td>
                                        <td>${escapeHtml((data.data.assigned_user && (data.data.assigned_user.name || data.data.assigned_user.full_name || data.data.assigned_user.first_name)) || data.data.assigned_to || '')}</td>
                                        <td>${escapeHtml(data.data.due_date ? data.data.due_date.substring(0,10) : '')}</td>
                                        <td><span class="badge badge-warning">${escapeHtml(data.data.priority || '')}</span></td>
                                        <td><span class="badge badge-info">${escapeHtml(data.data.status || '')}</span></td>
                                        <td>
                                            <button class="btn btn-secondary" style="padding:3px 6px;font-size:0.6rem;" onclick="viewItem('${escapeHtml(data.data.task_id || data.data.id)}')">View</button>
                                            <button class="btn btn-primary" style="padding:3px 6px;font-size:0.6rem;margin-left:8px;" onclick="openEditTask(${data.data.id})">Edit</button>
                                            <button class="btn btn-danger" style="padding:3px 6px;font-size:0.6rem;margin-left:8px;" onclick="deleteTask(${data.data.id})">Delete</button>
                                        </td>
                                    `;
                                }
                            } else {
                                const tr = document.createElement('tr');
                                tr.setAttribute('data-task-id', data.data.id);
                                tr.innerHTML = `
                                    <td><strong>${escapeHtml(data.data.task_id || 'TSK-' + (data.data.id || ''))}</strong></td>
                                    <td>${escapeHtml(data.data.title)}</td>
                                    <td>${escapeHtml((data.data.assigned_user && (data.data.assigned_user.name || data.data.assigned_user.full_name || data.data.assigned_user.first_name)) || data.data.assigned_to || '')}</td>
                                    <td>${escapeHtml(data.data.due_date ? data.data.due_date.substring(0,10) : '')}</td>
                                    <td><span class="badge badge-warning">${escapeHtml(data.data.priority || '')}</span></td>
                                    <td><span class="badge badge-info">${escapeHtml(data.data.status || '')}</span></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding:3px 6px;font-size:0.6rem;" onclick="viewItem('${escapeHtml(data.data.task_id || data.data.id)}')">View</button>
                                        <button class="btn btn-primary" style="padding:3px 6px;font-size:0.6rem;margin-left:8px;" onclick="openEditTask(${data.data.id})">Edit</button>
                                        <button class="btn btn-danger" style="padding:3px 6px;font-size:0.6rem;margin-left:8px;" onclick="deleteTask(${data.data.id})">Delete</button>
                                    </td>
                                `;
                                tbody.insertBefore(tr, tbody.firstChild);
                            }
                        }
                    } catch (e) {
                        // ignore DOM insertion errors
                    }
                } else {
                    const msg = (data && data.message) ? data.message : 'Failed to save task';
                    showToaster('error', 'Error', msg);
                }
            }).catch(err => {
                console.error(err);
                showToaster('error', 'Error', err.message || 'Network or server error while saving task');
            }).finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                // reset editing state
                if (form) form.removeAttribute('data-editing');
                // reset submit button text if present
                if (submitBtn) submitBtn.textContent = submitBtn.textContent.replace(/Update|Creating|Create/gi, 'Create Task');
            });
    }

    // If any submit requests were queued before the real handler loaded, process them now
    (function(){
        try {
            var q = window.__submitTaskQueue || [];
            if (q && q.length) {
                delete window.__submitTaskQueue;
                q.forEach(function(args){
                    try { window.submitTaskForm.apply(null, args); } catch(e) { console.error(e); }
                });
            }
        } catch(e) { /* ignore */ }
    })();

    // Open edit modal and populate fields
    window.openEditTask = function(id) {
        fetch(`/tasks/${id}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(resp => {
                if (resp && resp.success && resp.data) {
                    const t = resp.data;
                    // ensure modal exists
                    if (typeof window.showTaskModal === 'function') window.showTaskModal();
                    // populate fields
                    document.getElementById('task-id').value = t.task_id || (`TSK-${t.id}`);
                    document.getElementById('task-title').value = t.title || '';
                    document.getElementById('task-desc').value = t.description || '';
                    if (document.getElementById('task-assigned')) document.getElementById('task-assigned').value = t.assigned_to || '';
                    document.getElementById('task-due').value = t.due_date ? t.due_date.substring(0,10) : '';
                    document.getElementById('task-priority').value = t.priority || 'Normal';
                    document.getElementById('task-status').value = t.status || 'Not Started';
                    document.getElementById('task-related').value = t.related || '';
                    const form = document.getElementById('task-create-form');
                    if (form) form.setAttribute('data-editing', t.id);
                    // update submit button text
                    const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.btn-primary');
                    if (submitBtn) submitBtn.textContent = 'Update Task';
                } else {
                    showToaster('error', 'Error', 'Failed to fetch task to edit');
                }
            }).catch(err => {
                console.error(err);
                showToaster('error', 'Error', 'Network error while fetching task');
            });
    }

    // Delete task after confirmation
    window.deleteTask = function(id) {
        if (!confirm('Delete this task? This action can be undone.')) return;
        fetch(`/tasks/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            credentials: 'same-origin'
        }).then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const row = document.querySelector(`#tasks-tbody tr[data-task-id="${id}"]`);
                if (row && row.parentElement) row.parentElement.removeChild(row);
                showToaster('success', 'Deleted', 'Task deleted');
            } else {
                showToaster('error', 'Error', (data && data.message) ? data.message : 'Failed to delete task');
            }
        }).catch(err => {
            console.error(err);
            showToaster('error', 'Error', 'Network error while deleting task');
        });
    }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function() {
                    const moduleId = this.getAttribute('data-module');
                    if (moduleId) {
                        showModule(moduleId);
                    }
                });
            });

            document.getElementById('modal-overlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal();
                }
            });
            // Defensive: rebind inline onclick handlers that reference showTaskModal
            try {
                document.querySelectorAll('[onclick="showTaskModal()"]')
                    .forEach(el => {
                        el.onclick = function(e) {
                            e.preventDefault();
                            if (typeof window.showTaskModal === 'function') return window.showTaskModal();
                            // fallback: dispatch event so modal-creator can react
                            document.dispatchEvent(new Event('openTaskModal'));
                        };
                    });
            } catch (e) {
                // ignore
            }
            // Attach submit handler for task create form to use our AJAX submit
            const taskForm = document.getElementById('task-create-form');
            if (taskForm) {
                taskForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitTaskForm();
                });
            }
            // Delegated click handler: only call submitTaskForm for buttons explicitly marked data-ajax="true"
            document.addEventListener('click', function (e) {
                try {
                    const btn = e.target.closest && e.target.closest('#task-create-form .btn-primary');
                    if (btn && btn.getAttribute('data-ajax') === 'true') {
                        e.preventDefault();
                        submitTaskForm();
                    }
                } catch (err) {
                    // ignore
                }
            });
        });

        // Form error helpers
        function clearFormErrors() {
            document.querySelectorAll('#task-create-form .field-error').forEach(el => el.remove());
        }

        function showFormErrors(errors) {
            // errors is object: { field: [messages] }
            Object.keys(errors).forEach(field => {
                const el = document.getElementById('task-' + field) || document.getElementById(field) || null;
                const messages = errors[field];
                if (el && messages && messages.length) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'field-error';
                    wrapper.style.color = '#c53030';
                    wrapper.style.fontSize = '0.85rem';
                    wrapper.style.marginTop = '6px';
                    wrapper.textContent = messages.join(' ');
                    el.parentNode && el.parentNode.appendChild(wrapper);
                }
            });
        }

        // If any open/edit/view calls were queued before the real handlers loaded, flush them now.
        (function(){
            try {
                const flush = (queueName, fnName) => {
                    const q = window[queueName] || [];
                    if (q && q.length && typeof window[fnName] === 'function') {
                        delete window[queueName];
                        q.forEach(function(args){
                            try { window[fnName].apply(null, args); } catch(e) { console.error(e); }
                        });
                    }
                };
                // run after next tick so functions have chance to initialize
                setTimeout(() => {
                    flush('__openEditTaskQueue','openEditTask');
                    flush('__viewItemQueue','viewItem');
                    flush('__submitTaskQueue','submitTaskForm');
                }, 50);
            } catch(e) { /* ignore */ }
        })();
</script>
@endsection
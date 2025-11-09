@extends('layouts.app')

@section('title', 'Workflow')
@section('page_title', 'Workflow')

@section('content')
    <div class="content">
        <div id="workflow" class="module active">
            <div class="tabs">
                <div class="tab-nav">
                    <button class="tab-button active" data-tab="list">List View</button>
                    <button class="tab-button" data-tab="calendar">Calendar View</button>
                </div>
            </div>

            <div id="workflow-list" class="tab-content active">
                    <div class="module-header">
                    <button class="btn btn-primary" id="create-task-btn"><i class="ti ti-plus"></i> Create Task</button>
                    <button class="btn btn-secondary" id="export-tasks-btn" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>

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
                                        <button type="button" class="btn btn-secondary" data-action="view" data-id="{{ $task->id }}" style="padding: 3px 6px; font-size: 0.6rem;">View</button>
                                        <button type="button" class="btn btn-primary" data-action="edit" data-id="{{ $task->id }}" style="padding: 3px 6px; font-size: 0.6rem; margin-left:8px;">Edit</button>
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
                        <h3 id="task-modal-title">Create Task</h3>
                        <button class="close btn-close" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="task-create-form" action="{{ route('tasks.store') }}" method="POST">
                            @csrf
                            <div class="form-group"><label class="form-label">Task ID</label><input type="text" class="form-input" id="task-id" readonly></div>
                            <div class="form-group"><label class="form-label">Task Title</label><input type="text" name="title" value="{{ old('title') }}" class="form-input" id="task-title" placeholder="Task title" required></div>
                            <div class="form-group"><label class="form-label">Description</label><textarea name="description" id="task-desc" class="form-input" rows="3" placeholder="Task description">{{ old('description') }}</textarea></div>

                            @php
                                $assignableUsers = App\Models\User::whereDoesntHave('roles', function($q){ $q->where('name','customer'); })->orderBy('first_name')->take(200)->get();
                            @endphp
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                <div class="form-group"><label class="form-label">Assigned To</label><select name="assigned_to" id="task-assigned" class="form-select"><option value="">Select user</option>@foreach($assignableUsers as $u)<option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->full_name }}</option>@endforeach</select></div>
                                <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" id="task-due" value="{{ old('due_date') }}" class="form-input"></div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:12px;">
                                <div class="form-group"><label class="form-label">Priority</label><select name="priority" id="task-priority" class="form-select"><option value="low" {{ old('priority')=='low' ? 'selected' : '' }}>Low</option><option value="normal" {{ old('priority')=='' || old('priority')=='normal' ? 'selected' : '' }}>Normal</option><option value="high" {{ old('priority')=='high' ? 'selected' : '' }}>High</option><option value="urgent" {{ old('priority')=='urgent' ? 'selected' : '' }}>Urgent</option></select></div>
                                <div class="form-group"><label class="form-label">Status</label><select name="status" id="task-status" class="form-select"><option value="not_started" {{ old('status')=='not_started' ? 'selected' : '' }}>Not Started</option><option value="in_progress" {{ old('status')=='' || old('status')=='in_progress' ? 'selected' : '' }}>In Progress</option><option value="completed" {{ old('status')=='completed' ? 'selected' : '' }}>Completed</option></select></div>
                            </div>

                            <div class="form-group" style="margin-top:12px;"><label class="form-label">Related To</label><select name="related" id="task-related" class="form-select"><option value="">Select related item</option><option {{ old('related')=='ORD-2024-001 - Sales Order' ? 'selected' : '' }}>ORD-2024-001 - Sales Order</option><option {{ old('related')=='OF-2024-0156 - Production Order' ? 'selected' : '' }}>OF-2024-0156 - Production Order</option><option {{ old('related')=='QC Batch #1234' ? 'selected' : '' }}>QC Batch #1234</option></select></div>

                            <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:18px;"><button type="submit" class="btn btn-primary">Create Task</button><button type="button" class="btn btn-secondary" id="task-cancel-btn">Cancel</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="workflow-calendar" class="tab-content">
                <div class="module-header">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button class="btn btn-secondary" id="cal-prev"><i class="ti ti-chevron-left"></i> Previous</button>
                        <h3 id="calendar-month-year" style="margin: 0; min-width: 180px; text-align: center;">September 2024</h3>
                        <button class="btn btn-secondary" id="cal-next">Next <i class="ti ti-chevron-right"></i></button>
                        <button class="btn btn-secondary" id="cal-today"><i class="ti ti-calendar-event"></i> Today</button>
                    </div>
                    <button class="btn btn-primary" id="create-task-btn-2"><i class="ti ti-plus"></i> Create Task</button>
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
                <button class="close" type="button" id="modal-close">&times;</button>
            </div>
            <div id="modal-body" class="modal-body" style="padding:12px;"></div>
        </div>
    </div>
    <style>
        /* Minimal modal-overlay styles used by workflow (kept local) */
        #modal-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); z-index: 2000; }
        #modal-overlay.show { display: flex; }
        #modal-overlay .modal-content { background: #fff; border-radius: 8px; max-width: 720px; width: 96%; box-shadow: 0 6px 24px rgba(0,0,0,0.2); }
        #modal-overlay .modal-header { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid #eee; }
        #modal-overlay .modal-body { padding:16px; max-height: 70vh; overflow:auto; }
    </style>

@section('scripts')
<script>
(function(){
    'use strict';

    // Utility functions
    function escapeHtml(str){ if (!str && str !== 0) return ''; return String(str).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]); }); }

    function showToaster(type, title, message, duration = 4000){
        const container = document.getElementById('toaster-container') || (function(){ const c = document.createElement('div'); c.id='toaster-container'; document.body.appendChild(c); return c; })();
        const toaster = document.createElement('div'); toaster.className = `toaster ${type}`;
        const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
        toaster.innerHTML = `<div class="toaster-icon">${icons[type]||''}</div><div class="toaster-content"><div class="toaster-title">${escapeHtml(title)}</div><div class="toaster-message">${escapeHtml(message)}</div></div><button class="toaster-close" aria-label="close">×</button>`;
        container.appendChild(toaster);
        setTimeout(()=>toaster.classList.add('show'),50);
        const close = toaster.querySelector('.toaster-close'); if (close) close.addEventListener('click', ()=>removeToaster(toaster));
        setTimeout(()=>removeToaster(toaster), duration);
    }
    function removeToaster(t){ if(!t) return; t.classList.remove('show'); setTimeout(()=>t.parentNode && t.parentNode.removeChild(t),300); }

    // Tabs
    function showTab(tabId){
        document.querySelectorAll('#workflow .tab-content').forEach(c=>c.classList.remove('active'));
        const el = document.getElementById('workflow-' + tabId);
        if (el) el.classList.add('active');
        document.querySelectorAll('#workflow .tab-button').forEach(b=>b.classList.remove('active'));
        const btn = document.querySelector(`#workflow .tab-button[data-tab="${tabId}"]`);
        if (btn) btn.classList.add('active');
    }

    // Modal helpers
    function showModal(title, html){
        const overlay = document.getElementById('modal-overlay');
        if (!overlay) return;
        if (title) document.getElementById('modal-title').textContent = title;
        if (typeof html === 'string') document.getElementById('modal-body').innerHTML = html;
        overlay.classList.add('show');
    }
    function hideModal(){ const overlay = document.getElementById('modal-overlay'); if (overlay) overlay.classList.remove('show'); }

    // Task modal show/hide
    function openTaskModal(){
        const modal = document.getElementById('task-create-modal');
        if (!modal) return;
        const form = modal.querySelector('form'); if (form) form.reset();
        document.getElementById('task-id').value = 'Auto-generated';
        // debug: ensure modal open is visible
        try { console.debug('openTaskModal: showing task-create-modal', modal); } catch(e){}
        modal.classList.add('show');
        // ensure display style in case CSS isn't applied as expected
        try{ modal.style.display = 'flex'; } catch(e){}
    }
    function closeTaskModal(){
        const modal = document.getElementById('task-create-modal');
        if (!modal) return;
        // hide visual state
        modal.classList.remove('show');
        try{ modal.style.display = 'none'; }catch(e){}
        // reset form and editing state
        try{
            const form = modal.querySelector('form');
            if (form){
                form.removeAttribute('data-editing');
                form.reset();
                const submitBtn = form.querySelector('button[type="submit"]'); if (submitBtn) submitBtn.textContent = 'Create Task';
            }
        }catch(e){ console.debug('closeTaskModal cleanup failed', e); }
    }

    // Load remote URL into central modal (AJAX)
    async function loadIntoModal(url, title){
        try{
            const bodyEl = document.getElementById('modal-body');
            const titleEl = document.getElementById('modal-title');
            if (titleEl && title) titleEl.textContent = title;
            if (bodyEl) bodyEl.innerHTML = '<div style="padding:12px">Loading...</div>';
            const res = await fetch(url, { credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest','Accept':'text/html'} });
            const text = await res.text();
            if (!res.ok) {
                console.error('loadIntoModal non-ok response', res.status, text);
                if (bodyEl) bodyEl.innerHTML = '<div style="padding:12px">Failed to load content</div>';
                showModal(title);
                return;
            }
            const parser = new DOMParser(); const doc = parser.parseFromString(text,'text/html');
            // prefer targeted fragments to avoid injecting the full layout
            let fragment = doc.querySelector('#task-create-form') || doc.querySelector('.content .p-4') || doc.querySelector('.p-4') || doc.querySelector('form') || doc.querySelector('main') || doc.querySelector('.content') || doc.body;
            if (fragment && bodyEl) {
                // If fragment is body (full page), try to narrow to '.content' to avoid duplicating nav
                if (fragment === doc.body) {
                    const narrowed = doc.querySelector('.content') || doc.querySelector('main');
                    if (narrowed) bodyEl.innerHTML = narrowed.innerHTML;
                    else bodyEl.innerHTML = text;
                } else {
                    bodyEl.innerHTML = fragment.innerHTML;
                }
                    // If server returned a full layout (sidebar/header present), show it inside an iframe so the route renders as-is
                    const looksLikeFullPage = !!(doc.querySelector('.sidebar') || doc.querySelector('.logo') || doc.querySelector('html'));
                    if (looksLikeFullPage) {
                        // Instead of using an iframe (which can break session/cookies or sandboxing),
                        // try to request a JSON representation of the resource and render it inside the modal.
                        try {
                            const jsonRes = await fetch(url, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
                            if (jsonRes.ok) {
                                const json = await jsonRes.json().catch(()=>null);
                                if (json && (json.data || json.title || json.id)){
                                    // Render a simple details fragment from JSON
                                    const t = json.data || json;
                                    const html = `<div style="display:flex;flex-direction:column;gap:8px;"><div><strong>${escapeHtml(t.title||'')}</strong></div><div>${escapeHtml(t.description||'')}</div><div>Assigned: ${escapeHtml(t.assigned_user?.full_name||t.assigned_to||'')}</div><div>Due: ${t.due_date ? escapeHtml(t.due_date.substring(0,10)) : ''}</div><div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" onclick="hideModal();">Close</button></div></div>`;
                                    if (bodyEl) bodyEl.innerHTML = html;
                                    initModalLinks(bodyEl);
                                    showModal(title || (t.task_id||t.id));
                                    return;
                                }
                            }
                        } catch(e){ console.warn('json fallback failed for modal iframe fallback', e); }

                        // last resort: open inside iframe so the user can still view the page
                        openInModalFrame(url, title);
                        return;
                    }
                // attach modal-friendly link handlers inside the injected content
                initModalLinks(bodyEl);
            } else if (bodyEl) {
                // if no fragment found, insert the raw response so user can still see it
                bodyEl.innerHTML = text;
                initModalLinks(bodyEl);
            }
            // ensure modal is visible even if fragment parsing didn't match selectors
            showModal(title);
        }catch(err){
            console.error('loadIntoModal', err);
            const bodyEl = document.getElementById('modal-body');
            if (bodyEl) bodyEl.innerHTML = '<div style="padding:12px">Failed to load content</div>';
            showModal('Error');
        }
    }

    // Open a URL inside an iframe within the central modal. Useful to render full routes (edit/show) inside the modal frame.
    function openInModalFrame(url, title){
        try{
            const bodyEl = document.getElementById('modal-body');
            const titleEl = document.getElementById('modal-title');
            if (titleEl && title) titleEl.textContent = title || '';
            if (!bodyEl) return;
            // clear previous content
            bodyEl.innerHTML = '';
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '70vh';
            iframe.style.border = '0';
            iframe.setAttribute('sandbox', 'allow-forms allow-scripts allow-same-origin allow-top-navigation-by-user-activation');
            // ensure iframe resizes responsively
            bodyEl.appendChild(iframe);
            showModal(title);
        }catch(e){ console.error('openInModalFrame error', e); showModal(title); }
    }

    // Make links inside modal content open via AJAX in the same modal when possible
    function initModalLinks(container){
        try{
            if (!container) return;
            const anchors = container.querySelectorAll('a');
            anchors.forEach(a => {
                // only handle same-origin links (relative links) and links without target=_blank
                const href = a.getAttribute('href');
                if (!href || href.startsWith('http')) return;
                a.addEventListener('click', function(e){
                    e.preventDefault();
                    const title = a.getAttribute('data-modal-title') || '';
                    loadIntoModal(href, title);
                });
            });
        }catch(e){ console.error('initModalLinks error', e); }
    }

    // Task CRUD handlers
    async function submitTaskForm(e){
        if (e && e.preventDefault) e.preventDefault();
        const form = document.getElementById('task-create-form'); if (!form) return;
        const title = (document.getElementById('task-title')?.value||'').trim();
        if (!title) { showToaster('error','Missing title','Please enter a task title'); return; }
        const payload = {
            title: title,
            description: document.getElementById('task-desc')?.value || null,
            assigned_to: document.getElementById('task-assigned')?.value || null,
            due_date: document.getElementById('task-due')?.value || null,
            priority: document.getElementById('task-priority')?.value || 'Normal',
            status: document.getElementById('task-status')?.value || 'Not Started',
            related: document.getElementById('task-related')?.value || null
        };

        // determine if editing
        const editing = form.getAttribute('data-editing');
        const url = editing ? `/tasks/${editing}` : '/tasks';
        const method = editing ? 'PUT' : 'POST';
        const headers = { 'Accept':'application/json', 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' };

        try{
            const res = await fetch(url, { method, headers, body: JSON.stringify(payload), credentials: 'same-origin' });
            const data = await (res.ok ? res.json().catch(()=>null) : res.json().catch(()=>null));
            if (!res.ok) {
                if (res.status === 419) throw new Error('Session expired. Please reload.');
                if (data && data.errors) showFormErrors(data.errors);
                throw new Error((data && (data.message||data.error)) || 'Failed to save task');
            }
            // success: update table
            if (data && data.success && data.data){
                const t = data.data;
                const tbody = document.getElementById('tasks-tbody');
                if (tbody){
                    if (editing){
                        const existing = tbody.querySelector(`tr[data-task-id="${editing}"]`);
                        if (existing){ existing.innerHTML = taskRowHtml(t); }
                    } else {
                        const tr = document.createElement('tr'); tr.setAttribute('data-task-id', t.id); tr.innerHTML = taskRowHtml(t); tbody.insertBefore(tr, tbody.firstChild);
                    }
                }
                closeTaskModal();
                showToaster('success', editing ? 'Task updated' : 'Task created', `${t.task_id || t.id} — ${t.title}`);
                try { if (window.refetchWorkflowCalendar) window.refetchWorkflowCalendar(); } catch(e) {}
            } else {
                throw new Error((data && data.message) || 'Failed to save task');
            }
        }catch(err){ console.error('submitTaskForm', err); showToaster('error','Error', err.message || 'Network error'); }
    }

    function taskRowHtml(t){
        return `<td><strong>${escapeHtml(t.task_id || 'TSK-' + (t.id||''))}</strong></td>`+
               `<td>${escapeHtml(t.title)}</td>`+
               `<td>${escapeHtml((t.assigned_user && (t.assigned_user.name || t.assigned_user.full_name)) || t.assigned_to || '')}</td>`+
               `<td>${escapeHtml(t.due_date ? t.due_date.substring(0,10) : '')}</td>`+
               `<td><span class="badge badge-warning">${escapeHtml(t.priority||'')}</span></td>`+
               `<td><span class="badge badge-info">${escapeHtml(t.status||'')}</span></td>`+
               `<td>`+
               `<button class="btn btn-secondary" data-action="view" data-id="${t.id}">View</button>`+
               `<button class="btn btn-primary" data-action="edit" data-id="${t.id}" style="margin-left:8px;">Edit</button>`+
               `<button class="btn btn-danger" data-action="delete" data-id="${t.id}" style="margin-left:8px;">Delete</button>`+
               `</td>`;
    }

    async function openEditTask(id){
        try{
            try{ console.debug('openEditTask: fetching task', id); } catch(e){}
            const res = await fetch(`/tasks/${id}`, { credentials: 'same-origin', headers:{'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch');
            // handle JSON or HTML gracefully
            const ct = (res.headers.get('content-type')||'').toLowerCase();
            let t = null;
            if (ct.indexOf('application/json') !== -1) {
                const data = await res.json().catch(()=>null);
                t = data && (data.data || data);
            } else {
                // fallback: parse HTML and extract fields from the edit form or from a fragment
                const text = await res.text().catch(()=>null);
                if (text){
                    const parser = new DOMParser(); const doc = parser.parseFromString(text,'text/html');
                    // look for JSON embedded or a form with inputs
                    const form = doc.querySelector('form#task-edit-form') || doc.querySelector('form');
                    if (form){
                        const getVal = (name) => { const el = form.querySelector(`[name="${name}"]`); return el ? el.value : null; };
                        t = {
                            id: id,
                            title: getVal('title') || '',
                            description: getVal('description') || '',
                            assigned_to: getVal('assigned_to') || '',
                            due_date: getVal('due_date') || '',
                            priority: getVal('priority') || '',
                            status: getVal('status') || '',
                            related: getVal('related') || ''
                        };
                    }
                }
            }
            if (!t) throw new Error('Task not found');
            openTaskModal();
            document.getElementById('task-id').value = t.task_id || ('TSK-' + t.id);
            document.getElementById('task-title').value = t.title || '';
            document.getElementById('task-desc').value = t.description || '';
            if (document.getElementById('task-assigned')) document.getElementById('task-assigned').value = t.assigned_to || '';
            document.getElementById('task-due').value = t.due_date ? (t.due_date.substring ? t.due_date.substring(0,10) : t.due_date) : '';
            document.getElementById('task-priority').value = t.priority || 'Normal';
            document.getElementById('task-status').value = t.status || 'Not Started';
            document.getElementById('task-related').value = t.related || '';
            const formEl = document.getElementById('task-create-form'); if (formEl) formEl.setAttribute('data-editing', t.id);
            const submitBtn = formEl && formEl.querySelector('button[type="submit"]'); if (submitBtn) submitBtn.textContent = 'Update Task';
        }catch(err){ console.error('openEditTask', err); showToaster('error','Error','Failed to load task'); }
    }

    async function viewItem(idOrIdentifier){
        // try numeric id first
        const numeric = /^[0-9]+$/.test(String(idOrIdentifier)) ? parseInt(idOrIdentifier,10) : null;
        const tryOpen = async (id)=>{
            try{
                const res = await fetch(`/tasks/${id}`, { credentials: 'same-origin', headers:{'Accept':'application/json'} }); if (!res.ok) throw new Error();
                const data = await res.json(); const t = data.data || data;
                const html = `<div style="display:flex;flex-direction:column;gap:8px;"><div><strong>${escapeHtml(t.title||'')}</strong></div><div>${escapeHtml(t.description||'')}</div><div>Assigned: ${escapeHtml(t.assigned_user?.full_name||t.assigned_to||'')}</div><div>Due: ${t.due_date ? t.due_date.substring(0,10) : ''}</div><div>Priority: ${escapeHtml(t.priority||'')}</div><div>Status: ${escapeHtml(t.status||'')}</div><div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" onclick="hideModal();">Close</button></div></div>`;
                showModal(`Task ${t.task_id || t.id}`, html);
                try{ const ov = document.getElementById('modal-overlay'); if (ov) ov.style.display = 'flex'; } catch(e){}
            }catch(err){ console.error('viewItem', err); showToaster('error','Error','Failed to load task'); }
        };
        if (numeric) return tryOpen(numeric);
        // try find in table by display identifier like TSK-001
        const rows = document.querySelectorAll('#tasks-tbody tr');
        for (const r of rows){ const strong = r.querySelector('td strong'); if (strong && strong.textContent.trim() === String(idOrIdentifier)) { const id = r.getAttribute('data-task-id'); if (id) return tryOpen(id); } }
        // fallback: try as id
        tryOpen(idOrIdentifier);
    }

    async function deleteTask(id){
        if (!confirm('Delete this task?')) return;
        try{
            const headers = {'X-CSRF-TOKEN':'{{ csrf_token() }}'};
            const res = await fetch(`/tasks/${id}`, { method:'DELETE', headers, credentials:'same-origin' });
            const data = await res.json();
            if (data && data.success){
                const row = document.querySelector(`#tasks-tbody tr[data-task-id="${id}"]`);
                if (row && row.parentNode) row.parentNode.removeChild(row);
                showToaster('success','Deleted','Task deleted');
                try { if (window.refetchWorkflowCalendar) window.refetchWorkflowCalendar(); } catch(e) {}
            } else {
                showToaster('error','Error',(data && data.message) || 'Delete failed');
            }
        }catch(err){ console.error('deleteTask',err); showToaster('error','Error','Network error while deleting task'); }
    }

    // Form error helpers
    function clearFormErrors(){ document.querySelectorAll('#task-create-form .field-error').forEach(el=>el.remove()); }
    function showFormErrors(errors){ Object.keys(errors||{}).forEach(field=>{ const el = document.getElementById('task-' + field) || document.getElementById(field); const msgs = errors[field]; if (el && msgs && msgs.length){ const wrapper = document.createElement('div'); wrapper.className='field-error'; wrapper.style.color='#c53030'; wrapper.style.fontSize='0.85rem'; wrapper.style.marginTop='6px'; wrapper.textContent = msgs.join(' '); el.parentNode && el.parentNode.appendChild(wrapper); } }); }

    // --- Export helpers for Tasks (CSV export of visible table rows) ---
    function _escapeCSV(val) {
        if (val === null || val === undefined) return '';
        const s = String(val);
        if (/[",\n\r]/.test(s)) return '"' + s.replace(/"/g, '""') + '"';
        return s;
    }

    function _arrayToCSV(header, rows) {
        const lines = [];
        lines.push(header.map(_escapeCSV).join(','));
        rows.forEach(r => lines.push(r.map(_escapeCSV).join(',')));
        return '\uFEFF' + lines.join('\n');
    }

    function _downloadCSV(filename, csv) {
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 5000);
    }

    function _formatDate(v) {
        if (!v) return '';
        try {
            // accept numeric timestamps (seconds or ms), ISO strings, or common date strings
            let d;
            const s = String(v).trim();
            if (/^\d{10}$/.test(s)) { d = new Date(parseInt(s,10) * 1000); }
            else if (/^\d{13}$/.test(s)) { d = new Date(parseInt(s,10)); }
            else { d = new Date(s); }
            if (isNaN(d.getTime())) return String(v);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return `${d.getDate().toString().padStart(2,'0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
        } catch(e){ return String(v); }
    }

    async function exportTasks(){
        try{
            const tbody = document.getElementById('tasks-tbody'); if (!tbody) return showToaster('error','Export failed','No tasks table found');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (!rows.length) return showToaster('info','No data','No tasks to export');
            const header = ['Task ID','Title','Assigned To','Due Date','Priority','Status'];
            const data = rows.map(r => {
                const id = (r.querySelector('td strong') && r.querySelector('td strong').textContent) || '';
                const title = (r.children[1] && r.children[1].textContent) || '';
                const assigned = (r.children[2] && r.children[2].textContent) || '';
                const rawDue = (r.children[3] && r.children[3].textContent) || '';
                const due = _formatDate(rawDue);
                const priority = (r.children[4] && r.children[4].textContent) || '';
                const status = (r.children[5] && r.children[5].textContent) || '';
                return [id.trim(), title.trim(), assigned.trim(), due.trim(), priority.trim(), status.trim()];
            });
            const csv = _arrayToCSV(header, data);
            const ts = new Date().toISOString().slice(0,10).replace(/-/g,'');
            _downloadCSV(`tasks-${ts}.csv`, csv);
            showToaster('success','Exported', `Exported ${data.length} tasks`);
        }catch(e){ console.error('Tasks export failed', e); showToaster('error','Export failed', e.message || 'Failed to export tasks'); }
    }

    // Modal link handler for in-table links
    function attachModalLinkHandler(){ document.getElementById('tasks-tbody')?.addEventListener('click', function(e){ const a = e.target.closest && e.target.closest('a.js-modal-link'); if (!a) return; e.preventDefault(); const href = a.getAttribute('href'); const title = a.getAttribute('data-modal-title') || ''; if (href) loadIntoModal(href, title); }); }

    // Delegated action handler for view/edit/delete buttons
    function attachActionDelegation(){ document.getElementById('tasks-tbody')?.addEventListener('click', function(e){ const btn = e.target.closest && e.target.closest('button[data-action]'); if (!btn) return; const action = btn.getAttribute('data-action'); const id = btn.getAttribute('data-id'); if (!action || !id) return; if (action === 'view') viewItem(id); else if (action === 'edit') openEditTask(id); else if (action === 'delete') deleteTask(id); }); }

    // Calendar nav handlers (if FullCalendar or other calendar is present, these will be used by bundled scripts too)
    function changeMonth(delta){ /* placeholder - full calendar controlled by bundled script */ }
    function goToToday(){ /* placeholder */ }

    // If FullCalendar isn't available (no front-end build), render a simple fallback list of events
    async function renderCalendarFallback(){
        const el = document.getElementById('workflow-fullcalendar');
        if (!el) return;
        // if the real calendar is present, don't render fallback
        if (window.workflowCalendar) return;
        try{
            el.innerHTML = '<div style="padding:12px">Loading tasks...</div>';
            const res = await fetch('/api/tasks/events', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch events');
            const events = await res.json();
            if (!Array.isArray(events) || events.length === 0){
                el.innerHTML = '<div style="padding:12px">No tasks found for calendar</div>';
                return;
            }
            // Render a compact list grouped by date
            const groups = {};
            events.forEach(ev=>{
                const d = ev.start ? ev.start.substring(0,10) : 'No date';
                groups[d] = groups[d] || [];
                groups[d].push(ev);
            });
            const container = document.createElement('div');
            container.style.padding = '8px';
            Object.keys(groups).sort().forEach(date => {
                const day = document.createElement('div');
                day.style.marginBottom = '10px';
                const h = document.createElement('div'); h.style.fontWeight = '600'; h.style.marginBottom='6px'; h.textContent = date;
                day.appendChild(h);
                groups[date].forEach(ev=>{
                    const row = document.createElement('div');
                    row.style.display='flex'; row.style.justifyContent='space-between'; row.style.alignItems='center'; row.style.padding='6px 8px'; row.style.border='1px solid #eee'; row.style.borderRadius='6px'; row.style.marginBottom='6px';
                    const left = document.createElement('div'); left.innerHTML = `<div style="font-weight:600">${escapeHtml(ev.title||'Untitled')}</div><div style="font-size:0.85rem;color:#666">${escapeHtml(ev.extendedProps?.priority||'')}</div>`;
                    const actions = document.createElement('div');
                    const viewBtn = document.createElement('button'); viewBtn.className='btn btn-secondary'; viewBtn.textContent='View'; viewBtn.style.marginRight='6px';
                    viewBtn.addEventListener('click', function(){ if (ev.url) loadIntoModal(ev.url, 'Task Details'); });
                    row.appendChild(left); row.appendChild(actions);
                    actions.appendChild(viewBtn);
                    container.appendChild(row);
                });
            });
            el.innerHTML = '';
            el.appendChild(container);
        }catch(e){ console.error('calendar fallback error', e); const el2=document.getElementById('workflow-fullcalendar'); if (el2) el2.innerHTML = '<div style="padding:12px">Unable to load calendar events</div>'; }
    }

    // Initialization on DOM ready
    document.addEventListener('DOMContentLoaded', function(){
        // tab buttons
        document.querySelectorAll('#workflow .tab-button').forEach(btn=> btn.addEventListener('click', function(){ const tab = this.getAttribute('data-tab'); if (tab) showTab(tab); }));
        // create task btns
        document.getElementById('create-task-btn')?.addEventListener('click', openTaskModal);
        document.getElementById('create-task-btn-2')?.addEventListener('click', openTaskModal);
    document.getElementById('export-tasks-btn')?.addEventListener('click', exportTasks);
        document.getElementById('task-cancel-btn')?.addEventListener('click', closeTaskModal);
        // close icons
        document.querySelectorAll('#task-create-modal .close, #task-create-modal .btn-close').forEach(el=>el.addEventListener('click', closeTaskModal));
        document.getElementById('modal-close')?.addEventListener('click', hideModal);
        document.getElementById('modal-overlay')?.addEventListener('click', function(e){ if (e.target === this) hideModal(); });

        // attach form handler
        const taskForm = document.getElementById('task-create-form'); if (taskForm){ taskForm.addEventListener('submit', submitTaskForm); }

        // attach modal link handlers & delegation
        attachModalLinkHandler(); attachActionDelegation();

        // enhance existing inline modal links in the table to open via AJAX
        document.querySelectorAll('a.js-modal-link').forEach(a=>{ a.addEventListener('click', function(e){ e.preventDefault(); const href = this.getAttribute('href'); const title = this.getAttribute('data-modal-title') || ''; if (href) loadIntoModal(href,title); }); });

        // process any pre-existing queues (if older inline scripts queued calls)
        try{
            const flush = (qName, fn) => { const q = window[qName] || []; if (q && q.length && typeof fn === 'function'){ delete window[qName]; q.forEach(args=>{ try{ fn.apply(null, args); }catch(e){ console.error(e); } }); } };
            setTimeout(()=>{ flush('__openEditTaskQueue', openEditTask); flush('__viewItemQueue', viewItem); flush('__submitTaskQueue', submitTaskForm); }, 20);
        }catch(e){}

        // expose some functions globally for compatibility with other inline code
        window.showTaskModal = openTaskModal; window.hideTaskModal = closeTaskModal; window.openEditTask = openEditTask; window.viewItem = viewItem; window.deleteTask = deleteTask; window.loadIntoModal = loadIntoModal;

        // Intercept form submissions inside the central modal and submit via AJAX when possible
        const modalBody = document.getElementById('modal-body');
        if (modalBody) {
            modalBody.addEventListener('submit', async function(ev){
                ev.preventDefault();
                const form = ev.target;
                if (!form || !form.action) return;
                try{
                    const methodInput = form.querySelector('input[name="_method"]');
                    const method = (methodInput ? methodInput.value : form.method || 'POST').toUpperCase();
                    const formData = new FormData(form);
                    const payload = {};
                    formData.forEach((v,k)=>{ payload[k]=v; });
                    const headers = {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'};
                    const res = await fetch(form.action, { method: method, headers, body: JSON.stringify(payload), credentials: 'same-origin' });
                    const data = await res.json().catch(()=>null);
                    if (!res.ok) {
                        if (data && data.errors) showFormErrors(data.errors);
                        throw new Error((data && (data.message||data.error)) || 'Failed to submit');
                    }
                    // If backend returned the saved task, update the table and close modal
                    if (data && data.success && data.data){
                        const t = data.data;
                        const tbody = document.getElementById('tasks-tbody');
                        if (tbody){
                            const existing = tbody.querySelector(`tr[data-task-id="${t.id}"]`);
                            if (existing){ existing.innerHTML = taskRowHtml(t); }
                            else { const tr = document.createElement('tr'); tr.setAttribute('data-task-id', t.id); tr.innerHTML = taskRowHtml(t); tbody.insertBefore(tr, tbody.firstChild); }
                        }
                        hideModal();
                        showToaster('success', data.message || 'Saved', t.title || '');
                    } else if (data && data.id) {
                        const t = data;
                        const tbody = document.getElementById('tasks-tbody');
                        if (tbody){ const existing = tbody.querySelector(`tr[data-task-id="${t.id}"]`); if (existing){ existing.innerHTML = taskRowHtml(t); } else { const tr = document.createElement('tr'); tr.setAttribute('data-task-id', t.id); tr.innerHTML = taskRowHtml(t); tbody.insertBefore(tr, tbody.firstChild); } }
                        hideModal();
                        showToaster('success','Saved', t.title || '');
                    } else {
                        // If response not JSON or didn't contain task, just close and reload optionally
                        hideModal();
                        showToaster('info','Saved','');
                    }
                }catch(err){ console.error('modal form submit', err); showToaster('error','Error', err.message || 'Failed to submit form'); }
            });
        }

        // If FullCalendar isn't bundled, render a simple fallback list so users still see tasks in the calendar area
        try { renderCalendarFallback(); } catch (e) { /* ignore */ }

    });
})();
</script>
@endsection

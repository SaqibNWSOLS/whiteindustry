@extends('layouts.app')

@section('title', 'Documents')
@section('page_title', 'Documents')

@section('content')
<div class="content">
    <div class="module-header">
        <h1 class="text-2xl font-semibold">Documents</h1>
        <div class="actions" style="display:flex; gap:8px;">
            <button class="btn btn-primary" id="btn-upload">Upload Document</button>
            <input id="doc-search" type="search" placeholder="Search documents..." class="search-input" />
        </div>
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3>All Documents</h3>
                <p class="text-sm text-gray-500">Manage uploaded documents, link to orders, products or customers.</p>
            </div>
            <div>
                <button class="btn btn-secondary" id="btn-bulk-download">Download Selected</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width:30px"><input id="chk-all" type="checkbox" /></th>
                        <th>File</th>
                        <th>Type</th>
                        <th>Related</th>
                        <th>Uploaded By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="documents-tbody"><tr><td colspan="7">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const documentsApi = '/api/documents';

    function escapeHtml(s) { if (s === null || s === undefined) return ''; return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c]); }

    document.addEventListener('DOMContentLoaded', function(){
        loadDocuments();
        document.getElementById('btn-upload').addEventListener('click', openUploadModal);
        document.getElementById('btn-bulk-download').addEventListener('click', bulkDownload);
        document.getElementById('chk-all').addEventListener('change', function(e){ document.querySelectorAll('#documents-tbody input[type=checkbox]').forEach(c=>c.checked = e.target.checked); });
        document.getElementById('doc-search').addEventListener('input', debounce(()=>{ loadDocuments(); }, 300));

        // delegate actions (view/download/delete)
        document.getElementById('documents-tbody').addEventListener('click', function(e){
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const id = btn.dataset.id;
            const action = btn.dataset.action;
            if (action === 'download') { window.location = `/api/documents/${id}/download`; }
            else if (action === 'delete') { deleteDocument(id); }
            else if (action === 'view') { viewDocument(id); }
        });
    });

    function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t = setTimeout(()=>fn(...a), ms); }; }

    async function loadDocuments(){
        const tbody = document.getElementById('documents-tbody');
        tbody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>';
        try {
            const q = encodeURIComponent(document.getElementById('doc-search').value || '');
            const res = await fetch(`${documentsApi}?per_page=1000&search=${q}`, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error(res.status + ' ' + res.statusText);
            const json = await res.json(); const list = Array.isArray(json)?json:(json.data||[]);
            if (!list.length) { tbody.innerHTML = '<tr><td colspan="7">No documents found</td></tr>'; return; }
            const rows = list.map(d=>{
                    const related = d.related_type ? `${escapeHtml(d.related_type)} #${escapeHtml(d.related_id)}` : '';
                    const uploader = d.uploader ? escapeHtml(d.uploader.full_name || ((d.uploader.first_name || d.uploader.last_name) ? ((d.uploader.first_name||'') + ' ' + (d.uploader.last_name||'')).trim() : '')) : (d.uploaded_by ? ('User #' + String(d.uploaded_by)) : '');
                    // determine href: if file_path points to public/uploads (uploaded_documents) use direct path, otherwise use /storage/
                    const fp = d.file_path || '';
                    const href = fp.startsWith('uploaded_documents/') || fp.startsWith('documents/') && !fp.startsWith('documents/') ? ('/' + fp) : ('/storage/' + fp);
                    // simple heuristic: if path begins with 'uploaded_documents' it's in public/, otherwise assume storage disk
                    const linkHref = fp.startsWith('uploaded_documents/') ? ('/' + fp) : ('/storage/' + fp);
                    return `<tr data-id="${d.id}"><td><input type="checkbox" value="${d.id}" /></td><td><a href="${linkHref}" target="_blank">${escapeHtml(d.file_name || d.name)}</a></td><td>${escapeHtml(d.type)}</td><td>${related}</td><td>${uploader}</td><td>${escapeHtml(d.created_at ? d.created_at.substring(0,10) : '')}</td><td style="white-space:nowrap"><div style="display:inline-flex;gap:6px;align-items:center;flex-wrap:nowrap"><button class="btn" data-action="view" data-id="${d.id}">View</button><button class="btn btn-secondary" data-action="download" data-id="${d.id}">Download</button><button class="btn btn-danger" data-action="delete" data-id="${d.id}">Delete</button></div></td></tr>`;
            }).join('');
            tbody.innerHTML = rows;
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="7">Error loading documents: ${escapeHtml(err.message || String(err))}</td></tr>`;
        }
    }

    function viewDocument(id){
        showModal(); document.getElementById('modal-title').textContent = 'Document'; document.getElementById('modal-body').innerHTML = '<div style="padding:12px">Loading...</div>';
        fetch(`/api/documents/${id}`, { credentials: 'same-origin', headers: {'Accept':'application/json'} }).then(r=>r.json()).then(json=>{
            const d = json.data || json;
            const fp = d.file_path || '';
            const linkHref = fp.startsWith('uploaded_documents/') ? ('/' + fp) : ('/storage/' + fp);
            const modalUploader = d.uploader ? (d.uploader.full_name || d.uploader.name || '') : (d.uploaded_by ? ('User #' + String(d.uploaded_by)) : '');
            document.getElementById('modal-body').innerHTML = `<div style="display:flex;flex-direction:column;gap:8px;"><div><strong>${escapeHtml(d.name)}</strong></div><div>File: <a href="${linkHref}" target="_blank">${escapeHtml(d.file_name)}</a></div><div>Type: ${escapeHtml(d.type)}</div><div>Uploaded by: ${escapeHtml(modalUploader)}</div><div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" onclick="window.location='/api/documents/${d.id}/download'">Download</button><button class="btn" onclick="hideModal()">Close</button></div></div>`;
        }).catch(e=>{ document.getElementById('modal-body').innerHTML = '<div>Error loading document</div>'; });
    }

    function openUploadModal(){
        showModal(); document.getElementById('modal-title').textContent = 'Upload Document'; document.getElementById('modal-body').innerHTML = `
            <form id="upload-form" enctype="multipart/form-data">
                <div class="form-group"><label class="form-label">Name</label><input name="name" class="form-input" required /></div>
                <div class="form-group"><label class="form-label">Type</label><select name="type" class="form-input"><option value="other">Other</option><option value="sop">SOP</option><option value="certificate">Certificate</option><option value="contract">Contract</option><option value="report">Report</option><option value="invoice">Invoice</option></select></div>
                <div class="form-group"><label class="form-label">File</label><input name="file" type="file" class="form-input" required /></div>
                <div class="form-group"><label class="form-label">Related Type</label><input name="related_type" class="form-input" /></div>
                <div class="form-group"><label class="form-label">Related ID</label><input name="related_id" type="number" class="form-input" /></div>
                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn" type="button" onclick="hideModal()">Cancel</button><button class="btn btn-primary" type="submit">Upload</button></div>
            </form>`;
        const form = document.getElementById('upload-form');
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const fd = new FormData(form);
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/api/documents', { method: 'POST', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token}, body: fd });
                if (!res.ok) {
                    const j = await res.json().catch(()=>null);
                    throw new Error(j?.message || 'Upload failed');
                }
                showToaster('success','Uploaded','Document uploaded'); hideModal(); loadDocuments();
            } catch (err) { showToaster('error','Upload failed', err.message || ''); }
        });
    }

    async function deleteDocument(id){
        if (!confirm('Delete this document?')) return;
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/api/documents/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'} });
            if (!res.ok) throw new Error('Delete failed');
            showToaster('success','Deleted','Document removed'); loadDocuments();
        } catch (err) { showToaster('error','Error', err.message||'Delete failed'); }
    }

    function bulkDownload(){
        const ids = Array.from(document.querySelectorAll('#documents-tbody input[type=checkbox]:checked')).map(i=>i.value);
        if (!ids.length) return showToaster('info','No selection','Select documents to download');
        // simple behavior: open each download in a new tab (browser may block popups depending on settings)
        ids.forEach(id => window.open(`/api/documents/${id}/download`, '_blank'));
    }
</script>
@endpush

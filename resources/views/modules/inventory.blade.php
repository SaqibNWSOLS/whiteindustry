@extends('layouts.app')

@section('title', 'Inventory')
@section('page_title', 'Inventory')

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <button class="tab-button active" onclick="showTab('inventory', 'raw', this)">Raw Materials</button>
            <button class="tab-button" onclick="showTab('inventory', 'packaging', this)">Packaging</button>
            <button class="tab-button" onclick="showTab('inventory', 'blend', this)">Blend</button>
            <button class="tab-button" onclick="showTab('inventory', 'final', this)">Final Products</button>
        </div>
    </div>

    <div id="inventory-raw" class="tab-content active">
        <div class="module-header">
            <button class="btn btn-primary" onclick="createItem('product','Raw Material')"><i class="ti ti-box"></i> Add Raw Material</button>
            <button class="btn btn-secondary" onclick="exportInventory('raw')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" class="search-input" placeholder="Search...">
        </div>
        <div class="dashboard-grid" style="margin-bottom: 20px;">
            <div class="card">
                <h3><span class="wi-highlight">Total Inventory Items</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">847</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Active items</div>
            </div>
            <div class="card">
                <h3><span class="wi-highlight">Low Stock Alerts</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">12</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Below minimum</div>
            </div>
            <div class="card">
                <h3><span class="wi-highlight">Total Value</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">DZD 487K</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Current inventory</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header"><h3>Raw Materials</h3></div>
            <table>
                <thead>
                    <tr><th>Code</th><th>Name</th><th>Stock</th><th>Min Stock</th><th>Unit</th><th>Location</th><th>Actions</th></tr>
                </thead>
                <tbody id="inventory-raw-tbody"><tr><td colspan="7">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>

    <div id="inventory-packaging" class="tab-content">
        <div class="module-header">
            <button class="btn btn-primary" onclick="createItem('product','Packaging')"><i class="ti ti-box"></i> Add Packaging</button>
            <button class="btn btn-secondary" onclick="exportInventory('packaging')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" class="search-input" placeholder="Search packaging...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Packaging Materials</h3></div>
            <table>
                <thead><tr><th>Code</th><th>Name</th><th>Stock</th><th>Min Stock</th><th>Unit</th><th>Location</th><th>Actions</th></tr></thead>
                <tbody id="inventory-pack-tbody"><tr><td colspan="7">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>

    <div id="inventory-blend" class="tab-content">
        <div class="module-header">
            <button class="btn btn-primary" onclick="createItem('product','Blend')"><i class="ti ti-package"></i> Add Blend</button>
            <button class="btn btn-secondary" onclick="exportInventory('blend')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" class="search-input" placeholder="Search blends...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Blends</h3></div>
            <table>
                <thead><tr><th>Code</th><th>Name</th><th>Stock</th><th>Min Stock</th><th>Unit</th><th>Location</th><th>Actions</th></tr></thead>
                <tbody id="inventory-blend-tbody"><tr><td colspan="7">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>

    <div id="inventory-final" class="tab-content">
        <div class="module-header">
            <button class="btn btn-primary" id="btn-add-final-product"><i class="ti ti-package"></i> Add Final Product</button>
            <button class="btn btn-secondary" onclick="exportInventory('final')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" class="search-input" placeholder="Search final products...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Final Products</h3></div>
            <table>
                <thead><tr><th>Code</th><th>Name</th><th>Stock</th><th>Unit</th><th>Value</th><th>Actions</th></tr></thead>
                <tbody id="inventory-final-tbody"><tr><td colspan="6">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const inventoryApi = '/api/inventory';

    // Preserve any existing createItem implementation and override for product creation
    (function(){
        try {
            if (typeof window.createItem === 'function') {
                window.__origCreateItem = window.createItem;
            }
        } catch(e){}

        window.createItem = function(type, label){
            // If creating a product from the inventory UI, show tailored modal
            if (type === 'product') {
                const modalTitle = label ? ('Add ' + label) : 'Add Product';
                showModal();
                const titleEl = document.getElementById('modal-title');
                const bodyEl = document.getElementById('modal-body');
                if (titleEl) titleEl.textContent = modalTitle;
                if (!bodyEl) return;
                // determine canonical product type
                let canonical = 'final_product';
                const l = (label || '').toString().toLowerCase();
                if (l.indexOf('raw') !== -1) canonical = 'raw_material';
                else if (l.indexOf('pack') !== -1) canonical = 'packaging';
                else if (l.indexOf('blend') !== -1) canonical = 'blend';

                bodyEl.innerHTML = `
                    <form id="inventory-create-form">
                        <div class="form-group"><label class="form-label">Material Code</label><input name="material_code" class="form-input" placeholder="Auto or provide code"></div>
                        <div class="form-group"><label class="form-label">Name</label><input name="name" class="form-input" required></div>
                        <div class="form-group"><label class="form-label">Type</label>
                            <select name="type" class="form-input">
                                <option value="raw_material" ${canonical==='raw_material'?'selected':''}>Raw Material</option>
                                <option value="packaging" ${canonical==='packaging'?'selected':''}>Packaging</option>
                                <option value="blend" ${canonical==='blend'?'selected':''}>Blend</option>
                                <option value="final_product" ${canonical==='final_product'?'selected':''}>Final Product</option>
                            </select>
                        </div>
                        <div style="display:flex;gap:12px;">
                            <div class="form-group" style="flex:1"><label class="form-label">Initial Stock</label><input name="current_stock" type="number" step="0.001" class="form-input" value="0"></div>
                            <div class="form-group" style="width:160px"><label class="form-label">Minimum Stock</label><input name="minimum_stock" type="number" step="0.001" class="form-input" value="0"></div>
                        </div>
                        <div style="display:flex;gap:12px;">
                            <div class="form-group" style="flex:1"><label class="form-label">Unit</label><input name="unit" class="form-input" value="unit"></div>
                            <div class="form-group" style="width:160px"><label class="form-label">Unit Cost (DZD)</label><input name="unit_cost" type="number" step="0.000001" class="form-input" value="0"></div>
                        </div>
                        <div class="form-group"><label class="form-label">Supplier</label><input name="supplier" class="form-input"></div>
                        <div class="form-group"><label class="form-label">Storage Location</label><input name="storage_location" class="form-input"></div>
                        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-input" rows="3"></textarea></div>
                        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" type="submit">Create</button><button class="btn" type="button" onclick="hideModal()">Cancel</button></div>
                    </form>
                `;

                // handle submission
                const form = document.getElementById('inventory-create-form');
                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    try {
                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const headers = {'Content-Type':'application/json','Accept':'application/json'};
                        if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
                        const data = Object.fromEntries(new FormData(form).entries());
                        // normalize numeric fields
                        if (data.current_stock) data.current_stock = Number(data.current_stock);
                        if (data.minimum_stock) data.minimum_stock = Number(data.minimum_stock);
                        if (data.unit_cost) data.unit_cost = Number(data.unit_cost);
                        const res = await fetch(inventoryApi, { method: 'POST', credentials: 'same-origin', headers, body: JSON.stringify(data) });
                        const json = await res.json().catch(()=>null);
                        if (!res.ok) {
                            const msg = json && json.message ? json.message : 'Failed to create item';
                            showToaster('error','Error', msg);
                            return;
                        }
                        showToaster('success','Created','Inventory item created');
                        hideModal();
                        if (typeof loadInventoryAll === 'function') loadInventoryAll();
                    } catch (err) {
                        console.error('create inventory error', err);
                        showToaster('error','Error','Failed to create inventory item');
                    }
                });
                return;
            }
            // fallback to original createItem if present
            if (typeof window.__origCreateItem === 'function') return window.__origCreateItem(type, label);
        };
    })();

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, function(c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c];
        });
    }

    async function loadInventoryStats() {
        try {
            const res = await fetch('/api/inventory/statistics', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) return;
            const s = await res.json();
            const cards = document.querySelectorAll('#inventory-raw .card');
            if (cards && cards.length >= 3) {
                cards[0].querySelector('div').textContent = s.total_items ?? '—';
                cards[1].querySelector('div').textContent = s.low_stock_alerts ?? '—';
                cards[2].querySelector('div').textContent = 'DZD ' + ((s.total_value ?? 0).toLocaleString());
            }
        } catch (e) { console.error('Failed to load inventory stats', e); }
    }

    async function renderInventoryRows(type, tbodyId, q = null) {
        // Map friendly tab types to API 'type' values expected by InventoryRequest
        const typeMap = {
            raw: 'raw_material',
            packaging: 'packaging',
            blend: 'blend',
            final: 'final_product'
        };
        const apiType = typeMap[type] || type;
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>';
        try {
            let url = inventoryApi + '?per_page=1000' + (apiType ? ('&type=' + encodeURIComponent(apiType)) : '');
            if (q) url += '&q=' + encodeURIComponent(q);
            const res = await fetch(url, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to load inventory');
            const json = await res.json();
            const list = Array.isArray(json) ? json : (json.data || []);
            // perform client-side search filtering if a query was provided (inventory API doesn't support q)
            let results = list;
            if (q && results && results.length) {
                const qq = q.toString().toLowerCase();
                results = results.filter(i => {
                    const hay = ((i.name||'') + ' ' + (i.material_code||'') + ' ' + (i.storage_location||'') + ' ' + (i.supplier||'')).toString().toLowerCase();
                    return hay.indexOf(qq) !== -1;
                });
            }
            // If nothing returned for blend (historical rows may live in product_type), fallback to client-side filter across all items
            if ((!results || results.length === 0) && apiType === 'blend') {
                const res2 = await fetch(inventoryApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
                if (res2.ok) {
                    const j2 = await res2.json();
                    const all = Array.isArray(j2) ? j2 : (j2.data || []);
                    const filtered = (all || []).filter(i => {
                        const t = (i.type || i.product_type || '').toString().toLowerCase();
                        return t === 'blend' || t === 'blended' || t === 'mix';
                    });
                    // apply q filter to filtered list if query provided
                    const finalFiltered = q ? filtered.filter(i => {
                        const hay = ((i.name||'') + ' ' + (i.material_code||'') + ' ' + (i.storage_location||'') + ' ' + (i.supplier||'')).toString().toLowerCase();
                        return hay.indexOf(q.toString().toLowerCase()) !== -1;
                    }) : filtered;
                    if (!finalFiltered.length) { tbody.innerHTML = '<tr><td colspan="7">No items found</td></tr>'; return; }
                    // render finalFiltered
                    const rows = finalFiltered.map(i => {
                        const actions = `<div style="display:inline-flex;gap:8px;white-space:nowrap;align-items:center;"><button class="btn btn-sm btn-secondary" data-action="view" data-id="${i.id}">View</button> <button class="btn btn-sm btn-primary" data-action="adjust" data-id="${i.id}">Adjust</button> <button class="btn btn-sm btn-secondary" data-action="edit" data-id="${i.id}">Edit</button> <button class="btn btn-sm btn-danger" data-action="delete" data-id="${i.id}">Delete</button></div>`;
                        return `<tr data-id="${i.id}"><td>${i.material_code||i.id}</td><td>${i.name}</td><td>${i.current_stock}</td><td>${i.minimum_stock}</td><td>${i.unit}</td><td>${i.storage_location||''}</td><td>${actions}</td></tr>`;
                    }).join('');
                    tbody.innerHTML = rows;
                    return;
                }
            }
            if (!results.length) { tbody.innerHTML = '<tr><td colspan="7">No items found</td></tr>'; return; }
            const rows = results.map(i => {
                // Build common action buttons: View, Adjust, Edit, Delete
                const actions = `<div style="display:inline-flex;gap:8px;white-space:nowrap;align-items:center;"><button class="btn btn-secondary" data-action="view" data-id="${i.id}">View</button> <button class="btn btn-primary" data-action="adjust" data-id="${i.id}">Adjust</button> <button class="btn btn-secondary" data-action="edit" data-id="${i.id}">Edit</button> <button class="btn btn-danger" data-action="delete" data-id="${i.id}">Delete</button></div>`;
                if (tbodyId === 'inventory-final-tbody') {
                    return `<tr data-id="${i.id}"><td>${i.material_code||i.id}</td><td>${i.name}</td><td>${i.current_stock}</td><td>${i.unit}</td><td>DZD ${(i.current_stock * (i.unit_cost||0)).toLocaleString()}</td><td>${actions}</td></tr>`;
                }
                return `<tr data-id="${i.id}"><td>${i.material_code||i.id}</td><td>${i.name}</td><td>${i.current_stock}</td><td>${i.minimum_stock}</td><td>${i.unit}</td><td>${i.storage_location||''}</td><td>${actions}</td></tr>`;
            }).join('');
            tbody.innerHTML = rows;
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7">Error loading items</td></tr>';
            console.error(e);
        }
    }

    function buildInventoryRow(i, isFinal) {
        const actions = `<div style="display:inline-flex;gap:8px;white-space:nowrap;align-items:center;"><button class="btn btn-secondary" data-action="view" data-id="${i.id}">View</button> <button class="btn btn-primary" data-action="adjust" data-id="${i.id}">Adjust</button> <button class="btn btn-secondary" data-action="edit" data-id="${i.id}">Edit</button> <button class="btn btn-danger" data-action="delete" data-id="${i.id}">Delete</button></div>`;
        if (isFinal) {
            return `<tr data-id="${i.id}"><td>${i.material_code||i.id}</td><td>${i.name}</td><td>${i.current_stock}</td><td>${i.unit}</td><td>DZD ${(i.current_stock * (i.unit_cost||0)).toLocaleString()}</td><td>${actions}</td></tr>`;
        }
        return `<tr data-id="${i.id}"><td>${i.material_code||i.id}</td><td>${i.name}</td><td>${i.current_stock}</td><td>${i.minimum_stock}</td><td>${i.unit}</td><td>${i.storage_location||''}</td><td>${actions}</td></tr>`;
    }

    function normalizeType(item) {
        // Strict mapping: prefer explicit `type` field only. Do not infer from `category`.
        // This ensures items only appear in the table matching their declared type.
        const t = (item.type || item.product_type || '').toString().trim().toLowerCase();
        if (!t) return null;
        if (t === 'raw_material' || t === 'raw') return 'raw_material';
        if (t === 'packaging' || t === 'package' || t === 'pack') return 'packaging';
        if (t === 'blend' || t === 'blended' || t === 'mix') return 'blend';
        if (t === 'final_product' || t === 'final' || t === 'finished') return 'final_product';
        return null;
    }

    async function loadInventoryAll() {
            const rawTbody = document.getElementById('inventory-raw-tbody');
            const packTbody = document.getElementById('inventory-pack-tbody');
            const blendTbody = document.getElementById('inventory-blend-tbody');
            const finalTbody = document.getElementById('inventory-final-tbody');
        if (rawTbody) rawTbody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>';
        if (packTbody) packTbody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>';
        if (finalTbody) finalTbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';
        try {
            const res = await fetch(inventoryApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to load inventory');
            const json = await res.json();
            const list = Array.isArray(json) ? json : (json.data || []);

            // Partition using normalized type
            const raws = [];
            const packs = [];
            const blends = [];
            const finals = [];
            const unknown = [];
            list.forEach(i => {
                const t = normalizeType(i);
                if (t === 'raw_material') raws.push(i);
                else if (t === 'packaging') packs.push(i);
                else if (t === 'blend') blends.push(i);
                else if (t === 'final_product') finals.push(i);
                else unknown.push(i);
            });

            // debug in console if things look off
            if (window && window.console && typeof window.console.debug === 'function') console.debug('Inventory partitions', { raw: raws.length, packaging: packs.length, blend: blends.length, final: finals.length, unknown: unknown.length });

            if (rawTbody) rawTbody.innerHTML = (raws.length ? raws.map(i => buildInventoryRow(i, false)).join('') : '<tr><td colspan="7">No items found</td></tr>');
            if (packTbody) packTbody.innerHTML = (packs.length ? packs.map(i => buildInventoryRow(i, false)).join('') : '<tr><td colspan="7">No items found</td></tr>');
            if (blendTbody) blendTbody.innerHTML = (blends.length ? blends.map(i => buildInventoryRow(i, false)).join('') : '<tr><td colspan="7">No items found</td></tr>');
            if (finalTbody) finalTbody.innerHTML = (finals.length ? finals.map(i => buildInventoryRow(i, true)).join('') : '<tr><td colspan="6">No items found</td></tr>');

            // Do not force unknown items into any tab. Log for debugging so authors can correct type fields.
            if (unknown.length) {
                if (window && window.console && typeof window.console.warn === 'function') console.warn('Inventory items with unknown type skipped from tab partitions', unknown.map(i=>i.id));
            }

            // refresh stats
            if (typeof loadInventoryStats === 'function') try { loadInventoryStats(); } catch (e) {}
        } catch (e) {
            if (rawTbody) rawTbody.innerHTML = '<tr><td colspan="7">Error loading items</td></tr>';
            if (packTbody) packTbody.innerHTML = '<tr><td colspan="7">Error loading items</td></tr>';
            if (finalTbody) finalTbody.innerHTML = '<tr><td colspan="6">Error loading items</td></tr>';
            console.error(e);
        }
    }

    // Export helpers: build CSV and trigger download per-tab
    function escapeCSV(val) {
        if (val === null || val === undefined) return '';
        const s = String(val);
        if (/[",\n\r]/.test(s)) {
            return '"' + s.replace(/"/g, '""') + '"';
        }
        return s;
    }

    function arrayToCSV(header, rows) {
        const lines = [];
        lines.push(header.map(escapeCSV).join(','));
        rows.forEach(r => {
            lines.push(r.map(escapeCSV).join(','));
        });
        return '\uFEFF' + lines.join('\n'); // prepend BOM for Excel compatibility
    }

    function downloadCSV(filename, csv) {
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(()=>URL.revokeObjectURL(url), 5000);
    }

    async function exportInventory(tabType) {
        try {
            const typeMap = { raw: 'raw_material', packaging: 'packaging', blend: 'blend', final: 'final_product' };
            const apiType = typeMap[tabType] || tabType;
            const res = await fetch(inventoryApi + '?per_page=1000' + (apiType ? ('&type=' + encodeURIComponent(apiType)) : ''), { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch inventory for export');
            const json = await res.json();
            const list = Array.isArray(json) ? json : (json.data || []);
            if (!list.length) { showToaster('info','No data','No items to export'); return; }

            // build CSV columns depending on tab
            let header = ['Code','Name','Stock','Min Stock','Unit','Location','Unit Cost','Value'];
            let rows = [];
            if (tabType === 'final') {
                header = ['Code','Name','Stock','Unit','Unit Cost','Value','Storage Location'];
                rows = list.map(i => [i.material_code||i.id, i.name||'', i.current_stock||0, i.unit||'', Number(i.unit_cost||0).toFixed(6), (Number(i.current_stock||0) * Number(i.unit_cost||0)).toFixed(2), i.storage_location||'' ]);
            } else {
                // raw or packaging
                rows = list.map(i => [i.material_code||i.id, i.name||'', i.current_stock||0, i.minimum_stock||0, i.unit||'', i.storage_location||'', Number(i.unit_cost||0).toFixed(6), (Number(i.current_stock||0) * Number(i.unit_cost||0)).toFixed(2) ]);
            }

            const csv = arrayToCSV(header, rows);
            const now = new Date();
            const ts = now.toISOString().slice(0,10).replace(/-/g,'');
            const filename = `inventory-${tabType}-${ts}.csv`;
            downloadCSV(filename, csv);
            showToaster('success','Exported', `Exported ${rows.length} items`);
        } catch (err) {
            console.error('Export failed', err);
            showToaster('error','Export failed', err.message || 'Failed to export inventory');
        }
    }

    function showInventoryItem(id) {
        showModal(); document.getElementById('modal-title').textContent = 'Inventory Item'; document.getElementById('modal-body').innerHTML = '<div style="padding:12px">Loading...</div>';
        fetch('/api/inventory/' + encodeURIComponent(id), { credentials: 'same-origin', headers: {'Accept':'application/json'} }).then(r=>{ if(!r.ok) throw new Error('Failed'); return r.json(); }).then(i=>{
            const item = i.data || i;
            document.getElementById('modal-body').innerHTML = `
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div><strong>${item.name}</strong> — ${item.material_code}</div>
                    <div>Stock: <strong>${item.current_stock}</strong> ${item.unit}</div>
                    <div>Min Stock: ${item.minimum_stock}</div>
                    <div>Location: ${item.storage_location}</div>
                    <div>Supplier: ${item.supplier}</div>
                    <div style="margin-top:8px;">Notes:<div style="padding:8px;background:#f7fafc;border-radius:6px;margin-top:6px;">${item.notes||''}</div></div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" onclick="hideModal(); setTimeout(()=>{ adjustInventory(${item.id}); },200)">Adjust Stock</button><button class="btn" onclick="hideModal()">Close</button></div>
                </div>
            `;
        }).catch(e=>{ document.getElementById('modal-body').innerHTML = '<div>Error loading item</div>'; });
    }

    function adjustInventory(id) {
        showModal(); document.getElementById('modal-title').textContent = 'Adjust Stock'; document.getElementById('modal-body').innerHTML = `
            <form id="adjust-form">
                <div class="form-group"><label class="form-label">Type</label><select name="type" class="form-input"><option value="in">In</option><option value="out">Out</option><option value="adjustment">Adjustment</option></select></div>
                <div class="form-group"><label class="form-label">Quantity</label><input name="quantity" type="number" step="0.001" class="form-input" required /></div>
                <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-input" rows="3"></textarea></div>
                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" type="submit">Apply</button><button class="btn" type="button" onclick="hideModal()">Cancel</button></div>
            </form>
        `;
        const form = document.getElementById('adjust-form');
        form.addEventListener('submit', async function(e){ e.preventDefault(); try{ const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); const data = Object.fromEntries(new FormData(form).entries()); const res = await fetch('/api/inventory/' + id + '/adjust-stock', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept':'application/json'}, body: JSON.stringify(data) }); if (!res.ok) throw new Error('Failed to adjust'); hideModal(); showToaster('success','Adjusted','Inventory updated'); loadInventoryAll(); }catch(err){ showToaster('error','Error',err.message||'Adjust failed'); } });
    }

    document.addEventListener('DOMContentLoaded', function(){
        if (document.getElementById('inventory-raw-tbody')) loadInventoryAll();
        document.addEventListener('click', function(e){
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const action = btn.getAttribute('data-action');
            const id = btn.getAttribute('data-id');
            if (!action || !id) return;
            if (action === 'view') showInventoryItem(id);
            else if (action === 'adjust') adjustInventory(id);
            else if (action === 'edit') editInventory(id);
            else if (action === 'delete') deleteInventory(id);
        });

        // Wire search inputs per-tab (debounced)
        function debounce(fn, wait) { let t = null; return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }; }
        try {
            const searchInputs = document.querySelectorAll('.tab-content .search-input');
            searchInputs.forEach(inp => {
                const container = inp.closest('.tab-content');
                if (!container) return;
                const tabId = container.id.replace('inventory-',''); // raw, packaging, blend, final
                const tbodyMap = { raw: 'inventory-raw-tbody', packaging: 'inventory-pack-tbody', blend: 'inventory-blend-tbody', final: 'inventory-final-tbody' };
                const handler = debounce(function(ev){
                    const q = ev.target.value.trim();
                    // for per-tab search, call renderInventoryRows for that tab
                    if (tabId && tbodyMap[tabId]) {
                        renderInventoryRows(tabId, tbodyMap[tabId], q || null);
                    }
                }, 350);
                inp.addEventListener('input', handler);
                inp.addEventListener('keydown', function(ev){ if (ev.key === 'Enter') { ev.preventDefault(); handler({ target: inp }); } });
            });
        } catch (e) { console.error('inventory search wiring failed', e); }
    });

    async function editInventory(id) {
        try {
            showModal(); document.getElementById('modal-title').textContent = 'Edit Inventory Item'; document.getElementById('modal-body').innerHTML = '<div style="padding:12px">Loading...</div>';
            const res = await fetch('/api/inventory/' + encodeURIComponent(id), { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to load');
            const json = await res.json(); const item = json.data || json;
            document.getElementById('modal-body').innerHTML = `
                <form id="edit-inventory-form">
                    <div class="form-group"><label class="form-label">Material Code</label><input name="material_code" class="form-input" value="${item.material_code||''}" required /></div>
                    <div class="form-group"><label class="form-label">Name</label><input name="name" class="form-input" value="${item.name||''}" required /></div>
                    <div class="form-group"><label class="form-label">Category</label><input name="category" class="form-input" value="${item.category||''}" /></div>
                    <div style="display:flex;gap:12px;"><div style="flex:1" class="form-group"><label class="form-label">Current Stock</label><input name="current_stock" type="number" step="0.001" class="form-input" value="${item.current_stock||0}" /></div><div style="width:200px" class="form-group"><label class="form-label">Minimum Stock</label><input name="minimum_stock" type="number" step="0.001" class="form-input" value="${item.minimum_stock||0}" /></div></div>
                    <div style="display:flex;gap:12px;"><div style="flex:1" class="form-group"><label class="form-label">Unit Cost (DZD)</label><input name="unit_cost" type="number" step="0.01" class="form-input" value="${item.unit_cost||0}" /></div><div style="width:200px" class="form-group"><label class="form-label">Unit</label><input name="unit" class="form-input" value="${item.unit||''}" /></div></div>
                    <div class="form-group"><label class="form-label">Type</label><select name="type" class="form-input"><option value="raw_material" ${item.type==='raw_material'?'selected':''}>Raw Material</option><option value="packaging" ${item.type==='packaging'?'selected':''}>Packaging</option><option value="blend" ${item.type==='blend'?'selected':''}>Blend</option><option value="final_product" ${item.type==='final_product'?'selected':''}>Final Product</option></select></div>
                    <div class="form-group"><label class="form-label">Supplier</label><input name="supplier" class="form-input" value="${item.supplier||''}" /></div>
                    <div class="form-group"><label class="form-label">Storage Location</label><input name="storage_location" class="form-input" value="${item.storage_location||''}" /></div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" type="submit">Save</button><button class="btn" type="button" onclick="hideModal()">Cancel</button></div>
                </form>
            `;
            const form = document.getElementById('edit-inventory-form');
            form.addEventListener('submit', async function(e){ e.preventDefault(); try{ const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); const data = Object.fromEntries(new FormData(form).entries()); const res = await fetch('/api/inventory/' + encodeURIComponent(id), { method: 'PUT', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept':'application/json'}, body: JSON.stringify(data) }); if (!res.ok) { const err = await res.json().catch(()=>null); throw new Error(err?.message || 'Update failed'); } hideModal(); showToaster('success','Updated','Inventory item updated'); loadInventoryAll(); }catch(err){ showToaster('error','Error', err.message||'Update failed'); } });
        } catch (e) { hideModal(); showToaster('error','Error','Failed to load item'); }
    }

    async function deleteInventory(id) {
        try {
            const ok = await showConfirm('Are you sure you want to delete this inventory item? This action cannot be undone.', 'Delete inventory');
            if (!ok) return;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('/api/inventory/' + encodeURIComponent(id), { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'} });
            if (!res.ok) throw new Error('Delete failed');
            showToaster('success','Deleted','Inventory item removed');
            loadInventoryAll();
        } catch (e) { showToaster('error','Error', e.message||'Delete failed'); }
    }

    // --- Manufactured product creation flow ---
    // Open a multi-step modal for selecting materials, packaging, previewing costs, and creating final product
    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('btn-add-final-product');
        if (btn) btn.addEventListener('click', openManufactureModal);
    });

    async function openManufactureModal() {
        showModal();
        document.getElementById('modal-title').textContent = 'Create Final Product (Manufactured)';
        document.getElementById('modal-body').innerHTML = `
            <div id="manufacture-steps">
                <div id="step-1">
                    <h4>Step 1 — Select Raw Materials and Percentages</h4>
                    <div id="materials-list"></div>
                    <div style="margin-top:8px;"><button class="btn btn-secondary" id="add-material-row">Add Material</button></div>
                    <div style="margin-top:10px; color:#666; font-size:0.9rem;">Total %: <span id="materials-total">0</span>%</div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;"><button class="btn" onclick="hideModal()">Cancel</button><button class="btn btn-primary" id="to-step-2">Next</button></div>
                </div>
                <div id="step-2" style="display:none;">
                    <h4>Step 2 — Select Packaging & Batch Size</h4>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <div style="flex:1" class="form-group"><label class="form-label">Packaging</label><select id="select-packaging" class="form-input"><option value="">(none)</option></select></div>
                        <div style="width:160px;margin-top:20px;"><button class="btn" id="btn-create-packaging">Create Packaging</button></div>
                    </div>
                    <div class="form-group"><label class="form-label">Packaging Volume per Unit (e.g., liters)</label><input id="packaging-volume" type="number" class="form-input" value="1" step="0.001" /></div>
                    <div class="form-group"><label class="form-label">Batch Size (units)</label><input id="batch-size" type="number" class="form-input" value="1" step="0.001" /></div>
                    <div id="step2-error" style="color:#c53030;margin-top:6px;"></div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;"><button class="btn" id="back-to-step-1">Back</button><button class="btn btn-primary" id="to-step-3">Calculate</button></div>
                </div>
                <div id="step-3" style="display:none;">
                    <h4>Step 3 — Cost Preview</h4>
                    <div id="cost-preview">Loading...</div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;"><button class="btn" id="back-to-step-2">Back</button><button class="btn btn-primary" id="finalize-manufacture">Create Product</button></div>
                </div>
            </div>
        `;

        // populate materials and packaging selects
        await populateMaterialsRows();
        await populatePackagingSelect();

        document.getElementById('add-material-row').addEventListener('click', addMaterialRow);
        document.getElementById('to-step-2').addEventListener('click', () => {
            const total = Number(document.getElementById('materials-total').textContent || 0);
            const errEl = document.getElementById('materials-error');
            if (Math.abs(total - 100) > 0.5) {
                if (errEl) errEl.textContent = 'Materials percentages must total ~100%';
                showToaster('error','Percent error','Materials percentages should total ~100%');
                return;
            }
            if (errEl) errEl.textContent = '';
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('step-2').style.display = '';
        });
        document.getElementById('back-to-step-1').addEventListener('click', () => { document.getElementById('step-2').style.display='none'; document.getElementById('step-1').style.display=''; });
            document.getElementById('to-step-3').addEventListener('click', async () => {
            // call calc endpoint
            const materials = gatherMaterials();
            if (!materials.length) return showToaster('error','No materials','Add at least one material');
            const pack = document.getElementById('select-packaging').value || null;
            const batch = parseFloat(document.getElementById('batch-size').value) || 1;
            const packagingVolume = parseFloat(document.getElementById('packaging-volume').value) || 1;
            const step2Err = document.getElementById('step2-error');
            if (batch <= 0) { if (step2Err) step2Err.textContent = 'Batch size must be greater than 0'; return; }
            if (step2Err) step2Err.textContent = '';
            document.getElementById('step-2').style.display='none';
            document.getElementById('step-3').style.display='';
            document.getElementById('cost-preview').innerHTML = 'Calculating...';
            try {
                const res = await fetch('/api/inventory/calc-cost', { method: 'POST', headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, credentials: 'same-origin', body: JSON.stringify({ materials, packaging_id: pack || null, packaging_volume: packagingVolume, batch_size: batch }) });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message || 'Calculation failed');
                renderCostPreview(json.data);
            } catch (err) {
                console.error(err);
                if (document.getElementById('cost-preview')) document.getElementById('cost-preview').innerHTML = '<div style="color:#c53030">Calculation failed: '+escapeHtml(err.message||'')+'</div>';
                showToaster('error','Error', err.message||'Calculation failed');
                // keep user on step 3 so they can retry or go back
            }
        });

        document.getElementById('back-to-step-2').addEventListener('click', () => { document.getElementById('step-3').style.display='none'; document.getElementById('step-2').style.display=''; });
        // move to final form instead of directly prompting
        document.getElementById('finalize-manufacture').addEventListener('click', async () => {
            const preview = document.getElementById('cost-preview').dataset.preview ? JSON.parse(document.getElementById('cost-preview').dataset.preview) : null;
            if (!preview) return showToaster('error','Error','No preview data available');
            // show step-4 form
            if (!document.getElementById('step-4')) {
                const div = document.createElement('div'); div.id = 'step-4'; div.innerHTML = `
                    <h4>Step 4 — Final details</h4>
                    <form id="finalize-form">
                        <div class="form-group"><label class="form-label">Product Name</label><input name="name" id="final-name" class="form-input" required /></div>
                        <div class="form-group"><label class="form-label">Product Code</label><input name="material_code" id="final-code" class="form-input" /></div>
                        <div style="display:flex;gap:12px;"><div class="form-group" style="flex:1"><label class="form-label">Unit</label><input name="unit" id="final-unit" class="form-input" value="unit"/></div><div class="form-group" style="width:140px"><label class="form-label">Initial Stock</label><input name="initial_stock" id="final-initial" type="number" step="0.001" class="form-input"/></div></div>
                        <div class="form-group"><label class="form-label">Unit Cost (DZD)</label><input name="unit_cost" id="final-unit-cost" type="number" step="0.000001" class="form-input"/></div>
                        <div id="final-errors" style="color:#c53030;margin-top:6px;"></div>
                        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;"><button class="btn" type="button" id="back-to-step-3">Back</button><button class="btn btn-primary" type="submit">Create Product</button></div>
                    </form>
                `;
                document.getElementById('manufacture-steps').appendChild(div);
                document.getElementById('back-to-step-3').addEventListener('click', ()=>{ document.getElementById('step-4').style.display='none'; document.getElementById('step-3').style.display=''; });
                const form = document.getElementById('finalize-form');
                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    document.getElementById('final-errors').textContent = '';
                    const name = document.getElementById('final-name').value.trim();
                    const code = document.getElementById('final-code').value.trim();
                    const unit = document.getElementById('final-unit').value.trim() || 'unit';
                    const initial = parseFloat(document.getElementById('final-initial').value) || 0;
                    const unitCost = parseFloat(document.getElementById('final-unit-cost').value) || 0;
                    if (!name) { document.getElementById('final-errors').textContent = 'Name is required'; return; }
                    try {
                        const payload = {
                            material_code: code || `PRD-${Date.now()}`,
                            name: name,
                            type: 'final_product',
                            category: 'manufactured',
                            current_stock: 0,
                            initial_stock: initial,
                            minimum_stock: 0,
                            unit: unit,
                            unit_cost: unitCost,
                        };
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch('/api/inventory', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept':'application/json'}, body: JSON.stringify(payload) });
                        const json = await res.json().catch(()=>null);
                        if (!res.ok) {
                            // show validation errors inline if present
                            if (json && json.errors) {
                                const msgs = Object.values(json.errors).flat().join(' ');
                                document.getElementById('final-errors').textContent = msgs;
                            } else {
                                document.getElementById('final-errors').textContent = (json && json.message) ? json.message : 'Create failed';
                            }
                            return;
                        }
                        showToaster('success','Created','Final product created');
                        hideModal();
                        loadInventoryAll();
                    } catch (err) {
                        console.error(err);
                        document.getElementById('final-errors').textContent = err.message || 'Create failed';
                    }
                });
            }
            // populate final form defaults from preview
            document.getElementById('step-3').style.display='none';
            document.getElementById('step-4').style.display='';
            document.getElementById('final-unit-cost').value = preview.unit_cost_with_tax ?? preview.unit_cost_without_tax ?? '';
            document.getElementById('final-initial').value = preview.batch_size ?? 1;
        });
    }

    async function populateMaterialsRows() {
        const container = document.getElementById('materials-list');
        container.innerHTML = '';
        // load raw materials
        try {
            const res = await fetch('/api/inventory?per_page=1000&type=raw_material', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch materials');
            const json = await res.json();
            const list = Array.isArray(json) ? json : (json.data || []);
            if (!list.length) { container.innerHTML = '<div>No raw materials found</div>'; return; }
            // add one default row
            addMaterialRow(list);
            // store available materials on container for reuse
            container.dataset.available = JSON.stringify(list.map(i=>({id:i.id, name:i.name, unit_cost:i.unit_cost||0})));
        } catch (err) {
            container.innerHTML = '<div>Error loading materials</div>';
        }
    }

    function addMaterialRow(prefList) {
        const container = document.getElementById('materials-list');
        const available = prefList || JSON.parse(container.dataset.available || '[]');
        const idx = container.children.length;
        const row = document.createElement('div');
        row.style.display = 'flex'; row.style.gap = '8px'; row.style.marginTop='8px';
        const sel = document.createElement('select'); sel.className='form-input'; sel.style.flex='1';
        available.forEach(a => { const opt = document.createElement('option'); opt.value = a.id; opt.textContent = `${a.name} (DZD ${Number(a.unit_cost||0).toFixed(2)})`; sel.appendChild(opt); });
        const perc = document.createElement('input'); perc.type='number'; perc.step='0.01'; perc.value = (idx===0?100:0); perc.className='form-input'; perc.style.width='100px';
        const del = document.createElement('button'); del.className='btn'; del.textContent='Remove'; del.type='button';
        del.addEventListener('click', ()=>{ row.remove(); recalcMaterialsTotal(); });
        perc.addEventListener('input', recalcMaterialsTotal);
        sel.addEventListener('change', ()=>{});
        row.appendChild(sel); row.appendChild(perc); row.appendChild(del);
        container.appendChild(row);
        recalcMaterialsTotal();
    }

    function recalcMaterialsTotal() {
        const container = document.getElementById('materials-list');
        let total = 0;
        Array.from(container.querySelectorAll('input[type="number"]')).forEach(i => { total += parseFloat(i.value || 0); });
        document.getElementById('materials-total').textContent = (Math.round(total*100)/100).toString();
    }

    function gatherMaterials() {
        const container = document.getElementById('materials-list');
        const rows = Array.from(container.children);
        return rows.map(r => {
            const sel = r.querySelector('select'); const perc = r.querySelector('input[type="number"]');
            return { inventory_id: Number(sel.value), percentage: Number(perc.value) };
        }).filter(m => m.inventory_id && m.percentage > 0);
    }

    async function populatePackagingSelect() {
        const sel = document.getElementById('select-packaging');
        sel.innerHTML = '<option value="">(none)</option>';
        try {
            const res = await fetch('/api/inventory?per_page=1000&type=packaging', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed');
            const json = await res.json(); const list = Array.isArray(json)?json:(json.data||[]);
            list.forEach(p => { const o=document.createElement('option'); o.value=p.id; o.textContent=`${p.name} (DZD ${Number(p.unit_cost||0).toFixed(2)})`; sel.appendChild(o); });
        } catch (err) { /* ignore */ }
    }

    // Open a small in-modal packaging builder that lets user multi-select raw materials and create a packaging inventory
    document.addEventListener('click', function(e){
        const btn = e.target.closest('#btn-create-packaging');
        if (!btn) return;
        openPackagingBuilder();
    });

    async function openPackagingBuilder() {
        // build UI inside modal
        const container = document.createElement('div'); container.id = 'packaging-builder';
        container.style.padding = '8px'; container.style.background = '#fff'; container.style.border = '1px solid #e2e8f0'; container.style.marginTop = '12px';
        container.innerHTML = `
            <h4>Create Packaging</h4>
            <div id="pack-builder-list"></div>
            <div style="margin-top:8px;"><button class="btn" id="pack-add-row">Add Component</button></div>
            <div class="form-group"><label class="form-label">Packaging Name</label><input id="pack-name" class="form-input" /></div>
            <div class="form-group"><label class="form-label">Packaging Code</label><input id="pack-code" class="form-input" /></div>
            <div class="form-group"><label class="form-label">Packaging Volume (per package)</label><input id="pack-volume" type="number" step="0.001" class="form-input" value="1" /></div>
            <div id="pack-errors" style="color:#c53030;margin-top:6px;"></div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn" id="pack-cancel">Cancel</button><button class="btn btn-primary" id="pack-save">Save Packaging</button></div>
        `;
        const steps = document.getElementById('manufacture-steps');
        steps.appendChild(container);

        // populate one row with raw materials
        try {
            const res = await fetch('/api/inventory?per_page=1000&type=raw_material', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            const json = await res.json(); const list = Array.isArray(json)?json:(json.data||[]);
            const avail = list.map(i=>({id:i.id,name:i.name,unit_cost:i.unit_cost||0}));
            const listEl = document.getElementById('pack-builder-list');
            function addRow() {
                const idx = listEl.children.length;
                const row = document.createElement('div'); row.style.display='flex'; row.style.gap='8px'; row.style.marginTop='8px';
                const sel = document.createElement('select'); sel.className='form-input'; sel.style.flex='1';
                avail.forEach(a=>{ const o=document.createElement('option'); o.value=a.id; o.textContent=`${a.name} (DZD ${Number(a.unit_cost).toFixed(2)})`; sel.appendChild(o); });
                const perc = document.createElement('input'); perc.type='number'; perc.step='0.01'; perc.value=(idx===0?100:0); perc.className='form-input'; perc.style.width='100px';
                const del = document.createElement('button'); del.className='btn'; del.type='button'; del.textContent='Remove'; del.addEventListener('click', ()=>{ row.remove(); });
                row.appendChild(sel); row.appendChild(perc); row.appendChild(del);
                listEl.appendChild(row);
            }
            addRow();
            document.getElementById('pack-add-row').addEventListener('click', addRow);
            document.getElementById('pack-cancel').addEventListener('click', ()=>{ container.remove(); });
            document.getElementById('pack-save').addEventListener('click', async ()=>{
                const name = document.getElementById('pack-name').value.trim();
                const code = document.getElementById('pack-code').value.trim() || `PKG-${Date.now()}`;
                const vol = parseFloat(document.getElementById('pack-volume').value) || 1;
                const comps = Array.from(listEl.children).map(r=>{ const s=r.querySelector('select'); const p=r.querySelector('input'); return { inventory_id: Number(s.value), percentage: Number(p.value) }; }).filter(c=>c.inventory_id && c.percentage>0);
                if (!name) { document.getElementById('pack-errors').textContent = 'Packaging name required'; return; }
                if (!comps.length) { document.getElementById('pack-errors').textContent = 'Add at least one component'; return; }
                // create packaging via API
                try {
                    const payload = { material_code: code, name: name, type: 'packaging', category: 'packaging', current_stock: 0, minimum_stock: 0, unit: 'unit', composition: comps, packaging_volume: vol };
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res2 = await fetch('/api/inventory', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept':'application/json'}, body: JSON.stringify(payload) });
                    const j2 = await res2.json().catch(()=>null);
                    if (!res2.ok) { document.getElementById('pack-errors').textContent = (j2 && j2.message) ? j2.message : 'Create packaging failed'; return; }
                    // refresh packaging select and choose created
                    await populatePackagingSelect();
                    if (j2 && j2.data && j2.data.id) {
                        document.getElementById('select-packaging').value = j2.data.id;
                    }
                    showToaster('success','Created','Packaging created');
                    container.remove();
                } catch (err) { document.getElementById('pack-errors').textContent = err.message || 'Create failed'; }
            });
        } catch (err) { document.getElementById('pack-errors').textContent = 'Failed to load raw materials'; }
    }

    function renderCostPreview(data) {
        const el = document.getElementById('cost-preview');
        el.dataset.preview = JSON.stringify(data);
        el.innerHTML = `
            <div><strong>Batch size:</strong> ${data.batch_size}</div>
            <div><strong>Blend volume (total):</strong> ${data.blend_volume}</div>
            <div style="margin-top:8px;"><strong>Raw materials:</strong></div>
            <div style="margin-left:8px;">
                ${data.materials.map(m => `<div>${escapeHtml(m.name)} — ${m.percentage}% — qty ${m.quantity} — contrib DZD ${Number(m.contribution).toFixed(4)}</div>`).join('')}
            </div>
            <div style="margin-top:8px;"><strong>Raw total:</strong> DZD ${Number(data.raw_total).toFixed(4)}</div>
            <div><strong>Packaging total:</strong> DZD ${Number(data.pack_total).toFixed(4)}</div>
            <div><strong>Manufacture cost:</strong> DZD ${Number(data.manufacture_cost).toFixed(4)}</div>
            <div><strong>Risk cost:</strong> DZD ${Number(data.risk_cost).toFixed(4)}</div>
            <div><strong>Profit:</strong> DZD ${Number(data.profit).toFixed(4)}</div>
            <div style="margin-top:8px;"><label class="form-label">Commission %</label> <input id="commission-input" type="number" step="0.01" value="${Number(data.commission_percent||0).toFixed(2)}" class="form-input" style="width:160px;display:inline-block;margin-left:8px;" /></div>
            <div style="margin-top:8px;"><button class="btn" id="recalc-commission">Recalculate</button></div>
            <div style="margin-top:8px;"><strong>Commission:</strong> DZD ${Number(data.commission||0).toFixed(4)}</div>
            <div><strong>Tax:</strong> DZD ${Number(data.tax).toFixed(4)}</div>
            <div style="margin-top:10px;"><strong>Total (with tax):</strong> DZD ${Number(data.total_with_tax).toFixed(4)}</div>
            <div><strong>Unit cost (without tax):</strong> DZD ${Number(data.unit_cost_without_tax).toFixed(6)}</div>
            <div><strong>Unit cost (with commission):</strong> DZD ${Number(data.unit_cost_with_commission||data.unit_cost_without_tax).toFixed(6)}</div>
            <div><strong>Unit cost (with tax):</strong> DZD ${Number(data.unit_cost_with_tax).toFixed(6)}</div>
        `;

        // wire recalc button to re-run calculate endpoint with commission percent
        const recalcBtn = document.getElementById('recalc-commission');
        if (recalcBtn) {
            recalcBtn.addEventListener('click', async ()=>{
                const commission = parseFloat(document.getElementById('commission-input').value) || 0;
                // rebuild materials and packaging payload from modal inputs
                const materials = gatherMaterials();
                const pack = document.getElementById('select-packaging').value || null;
                const batch = parseFloat(document.getElementById('batch-size').value) || 1;
                const packagingVolume = parseFloat(document.getElementById('packaging-volume').value) || 1;
                try {
                    const res = await fetch('/api/inventory/calc-cost', { method: 'POST', headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, credentials: 'same-origin', body: JSON.stringify({ materials, packaging_id: pack || null, packaging_volume: packagingVolume, batch_size: batch, commission_percent: commission }) });
                    const json = await res.json();
                    if (!res.ok || !json.success) throw new Error(json.message || 'Recalc failed');
                    renderCostPreview(json.data);
                } catch (err) { showToaster('error','Error',err.message||'Recalc failed'); }
            });
        }
    }
</script>
@endpush

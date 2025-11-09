@extends('layouts.app')

@section('title', 'Orders')
@section('page_title', 'Orders')

@section('content')
<div class="content">
    <div class="module-header">
        <button class="btn btn-primary"><i class="ti ti-plus"></i> New Order</button>
        <button class="btn btn-secondary" onclick="exportOrders()" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
        <input type="search" class="search-input" placeholder="Search orders...">
    </div>

    <div class="dashboard-grid" style="margin-bottom: 20px;">
        <div class="card">
            <h3><span class="wi-highlight">Total Orders (YTD)</span></h3>
            <div id="orders-total-ytd" style="font-size: 2rem; font-weight: bold; color: #000;">—</div>
            <div id="orders-total-value" style="font-size: 0.7rem; color: #666; margin-top: 4px;">Total value: DZD —</div>
        </div>
        <div class="card">
            <h3><span class="wi-highlight">In Production</span></h3>
            <div id="orders-in-production" style="font-size: 2rem; font-weight: bold; color: #f59e0b;">—</div>
            <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Orders currently in production</div>
        </div>
        <div class="card">
            <h3><span class="wi-highlight">Pending</span></h3>
            <div id="orders-pending" style="font-size: 2rem; font-weight: bold; color: #f97316;">—</div>
            <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Orders pending</div>
        </div>
        <div class="card">
            <h3><span class="wi-highlight">Completed</span></h3>
            <div id="orders-completed" style="font-size: 2rem; font-weight: bold; color: #10b981;">—</div>
            <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Orders completed</div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h3>Order Management - Full Lifecycle</h3>
            <select class="form-select" style="width: auto;">
                <option>All Orders</option>
                <option>Pending Invoice</option>
                <option>Invoiced - Unpaid</option>
                <option>Fully Paid</option>
            </select>
        </div>
    <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Order Value</th>
                    <th>Order Status</th>
                    <th>Invoice Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <tr><td colspan="7">No orders loaded.</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function escapeHtml(s) { if (s === null || s === undefined) return ''; return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

const ordersApi = '/api/orders';

async function loadOrderStats() {
    try {
    const res = await fetch('/api/orders/statistics', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
        if (!res.ok) throw new Error('Failed to fetch stats');
        const s = await res.json();
        document.getElementById('orders-total-ytd').textContent = s.total_ytd ?? '—';
        document.getElementById('orders-total-value').textContent = 'Total value: DZD ' + ((s.total_value ?? 0).toLocaleString());
        document.getElementById('orders-in-production').textContent = s.in_production ?? '—';
        document.getElementById('orders-pending').textContent = s.pending ?? '—';
        document.getElementById('orders-completed').textContent = s.completed ?? '—';
    } catch (err) {
        console.error('Failed to load order stats', err);
    }
}

async function loadOrders(q = '') {
    const tbody = document.getElementById('orders-tbody');
    tbody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>';
    try {
        const url = ordersApi + (q ? ('?q=' + encodeURIComponent(q)) : '');
    const res = await fetch(url, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
        if (res.status === 401 || res.status === 403) {
            console.warn('Orders fetch returned', res.status);
            tbody.innerHTML = `<tr><td colspan="7">Authentication required. Please login and try again.</td></tr>`;
            return;
        }

        if (!res.ok) {
            const txt = await res.text().catch(()=>res.statusText);
            console.error('Failed to fetch orders:', res.status, txt);
            tbody.innerHTML = `<tr><td colspan="7">Failed to load orders: ${escapeHtml(res.status + ' ' + (res.statusText || ''))}</td></tr>`;
            return;
        }

        let data;
        try {
            data = await res.json();
        } catch (parseErr) {
            const text = await res.text().catch(()=>null);
            console.error('Failed to parse orders response as JSON', parseErr, text);
            tbody.innerHTML = `<tr><td colspan="7">Unexpected response from server. Check console/network.</td></tr>`;
            return;
        }
        const items = data.data || data;
        console.debug('orders response items:', items);
        if (!items || !items.length) { tbody.innerHTML = '<tr><td colspan="7">No orders found</td></tr>'; return; }
        try {
            tbody.innerHTML = items.map(o => `
            <tr>
                <td><strong>${o.order_number}</strong></td>
                <td>${escapeHtml(o.customer?.company_name ?? '')}</td>
                <td>${escapeHtml(o.product?.name ?? '')}</td>
                <td><strong>DZD ${Number(o.total_value).toLocaleString()}</strong></td>
                <td><span class="badge ${o.status === 'completed' ? 'badge-success' : (o.status === 'in_production' ? 'badge-info' : 'badge-warning')}">${o.status}</span></td>
                <td>${o.invoice ? `<div style="display:flex;flex-direction:column;gap:3px;"><span class="badge ${o.invoice.status === 'paid' ? 'badge-success':'badge-warning'}">${o.invoice.status}</span><a href="#" onclick="event.preventDefault(); viewInvoiceTracking('${o.invoice.invoice_number}'); return false;" style="font-size:0.65rem;color:rgb(20,54,25)">${o.invoice.invoice_number} →</a></div>` : '<div style="display:flex;flex-direction:column;gap:3px;"><span class="badge badge-warning">No Invoice</span><button class="btn btn-primary" onclick="createInvoiceFromOrder(\''+o.order_number+'\')">Create Invoice</button></div>'}</td>
                <td style="display:flex;gap:6px;align-items:center;"><button class="btn btn-sm btn-secondary" onclick="viewOrderDetails(${o.id})">View</button><button class="btn btn-sm btn-primary" onclick="editOrder(${o.id})">Edit</button><button class="btn btn-sm btn-danger" onclick="deleteOrder(${o.id})">Delete</button></td>
            </tr>
        `).join('');
        } catch (mapErr) {
            console.error('Error rendering orders table:', mapErr);
            tbody.innerHTML = `<tr><td colspan="7">Error rendering orders: ${escapeHtml(mapErr.message || String(mapErr))}</td></tr>`;
        }
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7">Error loading orders: ${escapeHtml(err.message)}</td></tr>`;
    }
}

// Server-side selects are used for customers and products inside the modal.


async function createOrderModal() {
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    modalTitle.textContent = 'Create Order';
    modalBody.innerHTML = `
    @php
        $products = App\Models\Product::all();
        $customers = App\Models\Customer::all();
    @endphp
        <form id="order-form">
            <div class="form-group"><label class="form-label">Order Number</label><input name="order_number" id="order-number-input" class="form-input" readonly placeholder="ORD-2024-xxx" /></div>
            <div class="form-group"><label class="form-label">Customer</label>
                <select name="customer_id" id="order-customer-id" class="form-select select2">
                    <option value="">Select a customer...</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{$customer->company_name}}</option>
                    @endforeach
                    </select>
                <div id="order-customer-results" class="typeahead-results" style="position:relative;z-index:60"></div>
            </div>
            <div class="form-group"><label class="form-label">Product</label>
                <select name="product_id" id="order-product-id" class="form-select select2">
                    <option value="">Select a product...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <div id="order-product-results" class="typeahead-results" style="position:relative;z-index:60"></div>
            </div>
            <div style="display:flex; gap:12px;">
                <div style="flex:1" class="form-group"><label class="form-label">Quantity</label><input name="quantity" class="form-input" type="number" step="0.001" min="0" value="0" required /></div>
                <div style="width:140px" class="form-group"><label class="form-label">Unit</label><select name="unit" class="form-input"><option value="L">L</option><option value="kg">kg</option><option value="pcs">pcs</option></select></div>
                <div style="width:220px" class="form-group"><label class="form-label">Total Value (DZD)</label><input name="total_value" class="form-input" type="number" step="0.01" min="0" value="0.00" required /></div>
            </div>
            <div style="display:flex; gap:12px;">
                <div style="flex:1" class="form-group"><label class="form-label">Order Date</label><input name="order_date" class="form-input" type="date" required /></div>
                <div style="flex:1" class="form-group"><label class="form-label">Delivery Date</label><input name="delivery_date" class="form-input" type="date" placeholder="mm / dd / yyyy" /></div>
            </div>
            <div class="form-group"><label class="form-label">Priority</label><select name="priority" class="form-input"><option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
            <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-input"><option value="pending">pending</option><option value="in_production">in_production</option><option value="completed">completed</option><option value="cancelled">cancelled</option></select></div>
            <div class="form-group"><label class="form-label">Special Instructions</label><textarea name="special_instructions" class="form-input" placeholder="Any special requirements"></textarea></div>
            <div style="display:flex; gap:12px; margin-top:12px;"><button class="btn btn-primary" type="submit">Create Order</button><button class="btn btn-secondary" type="button" onclick="hideModal()">Cancel</button></div>
        </form>
    `;
    // Server-side selects are rendered in the Blade template via $customers and $products.
    // Fetch a suggested order number and set default order date before showing the modal
    try {
    const sn = await fetch('/api/orders/suggested-number',{credentials:'same-origin', headers: {'Accept':'application/json'}});
        if (sn.ok) {
            const j = await sn.json();
            const inp = document.getElementById('order-number-input');
            if (inp && j.order_number) inp.value = j.order_number;
        }
    } catch (err) { /* ignore */ }
    const form = document.getElementById('order-form');
    if (form && !form.order_date.value) form.order_date.value = new Date().toISOString().split('T')[0];
    showModal();

    document.getElementById('order-form').addEventListener('submit', submitOrderForm);
    return Promise.resolve(true);
}

async function submitOrderForm(e) {
    e.preventDefault();
    const form = e.target;
    const body = Object.fromEntries(new FormData(form).entries());
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch(ordersApi, { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept':'application/json'}, body: JSON.stringify(body) });
        if (!res.ok) { const err = await res.json().catch(()=>null); throw new Error(err?.message || 'Create failed'); }
        hideModal(); loadOrders(); loadOrderStats(); showToaster('success','Order created','Order was created successfully.');
    } catch (err) { showToaster('error','Error','Failed to create order: ' + err.message); }
}

async function editOrder(id) {
    try {
    const res = await fetch(ordersApi + '/' + id, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
        if (!res.ok) throw new Error('Failed to load order');
        const o = await res.json();
        // wait for modal and selects to be populated
        const ready = await createOrderModal();
        if (!ready) throw new Error('Failed to prepare form');
    const form = document.getElementById('order-form');
    // set order number for edit
    const orderNumberInput = document.getElementById('order-number-input');
    if (orderNumberInput) orderNumberInput.value = o.order_number || '';
    // set select values (server-rendered selects)
    const custSelect = document.getElementById('order-customer-id');
    if (custSelect) custSelect.value = o.customer_id || '';
    const prodSelect = document.getElementById('order-product-id');
    if (prodSelect) prodSelect.value = o.product_id || '';
        form.quantity.value = o.quantity || '';
        form.unit.value = o.unit || '';
        form.total_value.value = o.total_value || '';
        form.order_date.value = o.order_date || '';
        form.delivery_date.value = o.delivery_date || '';
        form.priority.value = o.priority || 'normal';
        form.status.value = o.status || 'pending';
        form.special_instructions.value = o.special_instructions || '';
        form.removeEventListener('submit', submitOrderForm);
        form.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const body = Object.fromEntries(new FormData(form).entries());
            const r = await fetch(ordersApi + '/' + id, { method: 'PUT', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept':'application/json'}, body: JSON.stringify(body) });
            if (!r.ok) { const err = await r.json().catch(()=>null); showToaster('error','Error','Update failed: ' + (err?.message || r.statusText)); return; }
            hideModal(); loadOrders(); loadOrderStats(); showToaster('success','Order updated','Order was updated successfully.');
        });
    } catch (err) { showToaster('error','Error','Failed to load order: ' + err.message); }
}

async function deleteOrder(id) {
    if (!(await showConfirm('Delete this order?'))) return;
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const r = await fetch(ordersApi + '/' + id, { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'} });
        if (!r.ok) throw new Error('Delete failed');
        loadOrders(); loadOrderStats(); showToaster('success','Order deleted','Order was deleted successfully.');
    } catch (err) { showToaster('error','Error','Failed to delete order: ' + err.message); }
}

async function viewOrderDetails(id) {
    try {
    const res = await fetch(ordersApi + '/' + id, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
        if (!res.ok) throw new Error('Failed to load order');
        const o = await res.json();
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        modalTitle.textContent = `Order ${o.order_number}`;
        modalBody.innerHTML = `
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div><strong>Customer:</strong> ${escapeHtml(o.customer?.company_name ?? '')}</div>
                <div><strong>Product:</strong> ${escapeHtml(o.product?.name ?? '')}</div>
                <div style="display:flex;gap:12px;"><div><strong>Quantity:</strong> ${o.quantity} ${escapeHtml(o.unit ?? '')}</div><div><strong>Total:</strong> DZD ${Number(o.total_value).toLocaleString()}</div></div>
                <div><strong>Order Date:</strong> ${o.order_date ?? ''} <strong>Delivery:</strong> ${o.delivery_date ?? ''}</div>
                <div><strong>Priority:</strong> ${escapeHtml(o.priority ?? '')}</div>
                <div><strong>Status:</strong> ${escapeHtml(o.status ?? '')}</div>
                <div><strong>Special Instructions:</strong><div style="margin-top:6px;padding:8px;background:#f7fafc;border-radius:6px;">${escapeHtml(o.special_instructions ?? '')}</div></div>
            </div>
        `;
        if (o.invoice) {
            modalBody.innerHTML += `
                <hr />
                <div><h4>Invoice</h4>
                    <div><strong>Invoice #:</strong> <a href="#" onclick="event.preventDefault(); viewInvoiceTracking('${o.invoice.invoice_number}'); return false;">${o.invoice.invoice_number}</a></div>
                    <div><strong>Status:</strong> ${escapeHtml(o.invoice.status)}</div>
                    <div><strong>Amount:</strong> DZD ${Number(o.invoice.total_amount || o.total_value).toLocaleString()}</div>
                </div>
            `;
        } else {
            modalBody.innerHTML += `<div style="margin-top:12px;"><button class="btn btn-primary" onclick="createInvoiceFromOrder('${o.order_number}')">Create Invoice</button></div>`;
        }
        modalBody.innerHTML += `<div style="margin-top:12px;"><button class="btn btn-secondary" onclick="hideModal()">Close</button></div>`;
        showModal();
    } catch (err) { showToaster('error','Error','Failed to load order: ' + err.message); }
}

// wire search and initial load
document.addEventListener('DOMContentLoaded', function () {
    const search = document.querySelector('.module-header input.search-input');
    if (search) {
        let d;
        search.addEventListener('input', function (e) { clearTimeout(d); const q = e.target.value.trim(); d = setTimeout(() => loadOrders(q), 300); });
    }
    // wire New Order button to our modal
    const createBtn = document.querySelector('.module-header button');
    if (createBtn) {
        createBtn.addEventListener('click', function (e) { e.preventDefault(); createOrderModal(); });
    }

    // server-rendered selects are used; no typeahead attached here

    loadOrderStats();
    loadOrders();
});

    // --- Export helper for Orders ---
    function escapeCSV(val) {
        if (val === null || val === undefined) return '';
        const s = String(val);
        if (/[",\n\r]/.test(s)) return '"' + s.replace(/"/g, '""') + '"';
        return s;
    }

    function arrayToCSV(header, rows) {
        const lines = [];
        lines.push(header.map(escapeCSV).join(','));
        rows.forEach(r => lines.push(r.map(escapeCSV).join(',')));
        return '\uFEFF' + lines.join('\n');
    }

    function downloadCSV(filename, csv) {
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 5000);
    }

    async function exportOrders() {
        try {
            const res = await fetch(ordersApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch orders for export');
            const json = await res.json(); const list = json.data || json || [];
            if (!list.length) return showToaster('info','No data','No orders to export');

            const header = ['Order ID','Customer','Product','Order Value (DZD)','Order Status','Invoice Status','Invoice #'];
            const rows = list.map(o => [o.order_number||o.id, (o.customer?.company_name||''), (o.product?.name||''), Number(o.total_value||0).toFixed(2), o.status||'', (o.invoice ? o.invoice.status : 'No Invoice'), (o.invoice ? o.invoice.invoice_number : '')]);

            const csv = arrayToCSV(header, rows);
            const now = new Date(); const ts = now.toISOString().slice(0,10).replace(/-/g,'');
            downloadCSV(`orders-${ts}.csv`, csv);
            showToaster('success','Exported', `Exported ${rows.length} orders`);
        } catch (err) {
            console.error('Orders export failed', err);
            showToaster('error','Export failed', err.message || 'Failed to export orders');
        }
    }
</script>
@endpush

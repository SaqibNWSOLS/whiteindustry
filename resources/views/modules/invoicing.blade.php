@extends('layouts.app')

@section('title', 'Invoicing')
@section('page_title', 'Invoicing & Payments')

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <button class="tab-button active" onclick="showTab('invoicing', 'invoices', this)">Invoices</button>
            <button class="tab-button" onclick="showTab('invoicing', 'payments', this)">Payments</button>
            <button class="tab-button" onclick="showTab('invoicing', 'tracking', this); if (typeof loadTracking === 'function') loadTracking();">Tracking</button>
        </div>
    </div>

    <div id="invoicing-invoices" class="tab-content active">
        <div class="module-header">
            <button class="btn btn-primary" onclick="createItem('invoice')"><i class="ti ti-file-plus"></i> New Invoice</button>
            <button class="btn btn-secondary" onclick="exportInvoices()" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" class="search-input" placeholder="Search invoices...">
        </div>
        <div class="dashboard-grid" style="margin-bottom: 20px;">
            <div class="card">
                <h3><span class="wi-highlight">Outstanding</span></h3>
                <div id="invoices-outstanding-amount" style="font-size: 2rem; font-weight: bold; color: #000;">DZD 0</div>
                <div id="invoices-outstanding-count" style="font-size: 0.7rem; color: #666; margin-top: 4px;">0 unpaid invoices</div>
            </div>
            <div class="card">
                <h3><span class="wi-highlight">Overdue</span></h3>
                <div id="invoices-overdue-amount" style="font-size: 2rem; font-weight: bold; color: #ef4444;">DZD 0</div>
                <div id="invoices-overdue-count" style="font-size: 0.7rem; color: #666; margin-top: 4px;">0 overdue invoices</div>
            </div>
            <div class="card">
                <h3><span class="wi-highlight">Paid This Month</span></h3>
                <div id="invoices-paid-amount" style="font-size: 2rem; font-weight: bold; color: #10b981;">DZD 0</div>
                <div id="invoices-paid-count" style="font-size: 0.7rem; color: #666; margin-top: 4px;">0 invoices</div>
            </div>
        </div>
        <div class="table-container">
            <div class="table-header"><h3>All Invoices</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="invoices-tbody">
                    <tr><td colspan="6">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="invoicing-payments" class="tab-content">
        <div class="module-header">
            <button class="btn btn-primary" onclick="createItem('payment')"><i class="ti ti-cash"></i> Record Payment</button>
            <button class="btn btn-secondary" onclick="exportPayments()" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" class="search-input" placeholder="Search payments...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Payment History</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="payments-tbody">
                    <tr><td colspan="6">Loading...</td></tr>
                </tbody>
                </table>
        </div>
    </div>
    
    <div id="invoicing-tracking" class="tab-content">
        <div class="module-header" style="align-items:baseline;">
            <h3 style="margin:0">Payment Tracking Dashboard</h3>
            <div style="margin-left:auto;display:flex;gap:8px;align-items:center;">
                <button class="btn btn-secondary" onclick="exportTracking()"><i class="ti ti-download"></i> Export</button>
                <select id="tracking-filter" class="form-input" style="width:180px"><option value="all">All Invoices</option><option value="unpaid">Unpaid</option><option value="partial">Partially Paid</option><option value="paid">Fully Paid</option><option value="overdue">Overdue</option></select>
            </div>
        </div>

        <div class="dashboard-grid" style="margin-bottom: 20px;">
            <div class="card" id="track-payment-rate-card">
                <h3><span class="wi-highlight">Payment Rate</span></h3>
                <div id="track-payment-rate" style="font-size: 2rem; font-weight: bold; color: #10b981;">0%</div>
                <div id="track-payment-rate-desc" style="font-size: 0.8rem; color:#666;">On-time payments</div>
                <div style="margin-top:10px;"><div class="progress-bar"><div id="track-payment-rate-bar" class="progress-fill" style="width:0%"></div></div></div>
            </div>
            <div class="card" id="track-avg-time-card">
                <h3><span class="wi-highlight">Avg Payment Time</span></h3>
                <div id="track-avg-time" style="font-size: 2rem; font-weight: bold; color: #111;">0 days</div>
                <div id="track-avg-time-desc" style="font-size: 0.8rem; color:#666;">From invoice to payment</div>
            </div>
            <div class="card" id="track-collection-card">
                <h3><span class="wi-highlight">Collection Rate</span></h3>
                <div id="track-collection-rate" style="font-size: 2rem; font-weight: bold; color: #10b981;">0%</div>
                <div id="track-collection-desc" style="font-size: 0.8rem; color:#666;">Successfully collected</div>
            </div>
        </div>

        <div class="card" style="margin-bottom:18px;">
            <h3 style="margin-bottom:12px;">Invoice Payment Status Breakdown</h3>
            <div id="track-breakdown" style="display:flex; gap:12px; flex-wrap:wrap;"></div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:12px;">Detailed Payment Tracking</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                            <th>% Paid</th>
                            <th>Days Outstanding</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tracking-tbody"><tr><td colspan="9">Loading...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const invoicesApi = '/api/invoices';

    async function loadInvoices() {
        const tbody = document.getElementById('invoices-tbody');
        try {
            const res = await fetch(invoicesApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) {
                let text = await res.text().catch(()=>null);
                throw new Error('Failed to load invoices: ' + (text || res.statusText || res.status));
            }
            const json = await res.json();
            // API may return a paginated object {data: [...]} or a raw array
            const list = Array.isArray(json) ? json : (json.data || []);
            const rows = (list || []).map(inv => {
                const statusBadge = inv.status === 'paid' ? '<span class="badge badge-success">Paid</span>' : (inv.status === 'overdue' ? '<span class="badge badge-danger">Overdue</span>' : '<span class="badge badge-warning">Unpaid</span>');
                return `<tr data-id="${inv.id}"><td><strong>${inv.invoice_number || inv.id}</strong></td><td>${(inv.customer?.company_name||inv.customer?.contact_person||'Customer')}</td><td><strong>DZD ${inv.total_amount ?? 0}</strong></td><td style="color:#f57c00;font-weight:600;">DZD ${inv.balance ?? 0}</td><td>${statusBadge}</td><td><button class="btn btn-secondary" data-action="view" data-id="${inv.id}" style="padding:3px 6px;font-size:0.7rem;">View</button> <button class="btn btn-danger" data-action="delete" data-id="${inv.id}" style="padding:3px 6px;font-size:0.7rem;">Delete</button></td></tr>`;
            }).join('');
            tbody.innerHTML = rows || '<tr><td colspan="6">No invoices found</td></tr>';
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6">Error loading invoices</td></tr>`;
            console.error(e);
        }
        // refresh stats when invoices are loaded
        if (typeof loadInvoicingStats === 'function') try { loadInvoicingStats(); } catch (e) {}
    }

    async function deleteInvoice(id) {
        const ok = await showConfirm('Are you sure you want to delete this invoice?', 'Delete Invoice');
        if (!ok) return;
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/api/invoices/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'} });
            if (!res.ok) throw new Error('Delete failed');
            showToaster('success', 'Deleted', 'Invoice deleted');
            loadInvoices();
        } catch (e) {
            showToaster('error', 'Error', e.message || 'Delete failed');
        }
    }

    function viewInvoice(id) {
        showModal();
        document.getElementById('modal-title').textContent = 'Payment Tracking - INV-' + (typeof id === 'string' ? id : String(id));
        document.getElementById('modal-body').innerHTML = `<div style="padding:12px;">Loading...</div>`;
        fetch(`/api/invoices/${id}`, { credentials: 'same-origin', headers: {'Accept':'application/json'} }).then(r=>{ if(!r.ok) throw new Error('Failed to load'); return r.json(); }).then(inv=>{
            // set title from returned invoice number when available
            try { if (inv.invoice_number) document.getElementById('modal-title').textContent = 'Payment Tracking - ' + inv.invoice_number; } catch(e) {}
            // Build a richer layout similar to screenshot
            const paymentsHtml = (inv.payments && inv.payments.length) ? inv.payments.map(p=>`<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;"><div><strong>${p.payment_number||p.id}</strong><div style="font-size:12px;color:#6b7280">${p.payment_date? p.payment_date.split('T')[0] : ''} · ${p.method||''}</div></div><div style="text-align:right;"><strong>DZD ${p.amount}</strong></div></div>`).join('') : '<div style="padding:16px;background:#fff7ed;border-radius:6px">No payments have been recorded for this invoice yet.</div>';

            document.getElementById('modal-body').innerHTML = `
                <div style="padding:12px 18px;">
                    ${inv.order ? `<div style="background:#e6f2ff;padding:12px;border-radius:6px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center"><div style="font-size:0.9rem;color:#1e3a8a">Related Order<br><strong style='font-size:1.05rem'>${inv.order.order_number}</strong></div><div><button class='btn btn-secondary' onclick="hideModal(); setTimeout(()=>{ try { if (typeof viewOrderDetails === 'function') { viewOrderDetails(${inv.order.id}); } else { window.location.href = '/orders/${inv.order.id}'; } } catch(e) { window.location.href = '/orders/${inv.order.id}'; } },200)">View Order</button></div></div>` : ''}

                    <div style="display:flex;gap:24px;align-items:flex-start">
                        <div style="flex:1">
                            <div style="font-weight:700;font-size:1rem">Customer</div>
                            <div style="font-size:1.05rem;font-weight:600;margin-top:6px">${inv.customer?.company_name||inv.customer?.contact_person||''}</div>
                            <div style="margin-top:8px;color:#6b7280">Due Date<br><strong>${inv.due_date ? inv.due_date.split('T')[0] : (inv.issue_date?inv.issue_date.split('T')[0]:'')}</strong></div>
                        </div>
                        <div style="width:260px;">
                            <div style="display:flex;justify-content:space-between"><div>Invoice Total</div><div><strong>DZD ${inv.total_amount ?? 0}</strong></div></div>
                            <div style="display:flex;justify-content:space-between;margin-top:8px"><div>Amount Paid</div><div style="color:#10b981"><strong>DZD ${((inv.total_amount||0)-(inv.balance||0))}</strong></div></div>
                            <div style="display:flex;justify-content:space-between;margin-top:8px"><div>Balance Due</div><div style="color:#f97316"><strong>DZD ${inv.balance ?? 0}</strong></div></div>
                            <div style="margin-top:12px;">Payment Progress<br><div class="progress-bar"><div class="progress-fill" style="width:${inv.total_amount? (100*((inv.total_amount - (inv.balance||0))/inv.total_amount)) : 0}%"></div></div></div>
                            <div style="text-align:center;margin-top:10px"><span class="badge ${inv.status==='paid'?'badge-success':inv.status==='overdue'?'badge-danger':'badge-warning'}">${inv.status?inv.status.toUpperCase(): 'UNPAID'}</span></div>
                        </div>
                    </div>

                    <div style="margin-top:18px"><h4 style="margin:0 0 8px 0">Payments</h4>${paymentsHtml}</div>

                    <div style="display:flex;gap:12px;margin-top:14px"><button class="btn btn-primary" onclick="hideModal(); setTimeout(()=>createItem('payment'),200)">Record Payment</button><button class="btn btn-secondary" onclick="hideModal(); alert('Reminder sent');">Send Reminder</button><button class="btn" onclick="hideModal()">Close</button></div>
                </div>
            `;
        }).catch(e=>{ document.getElementById('modal-body').innerHTML = '<div>Error loading invoice</div>'; });
    }
    document.addEventListener('DOMContentLoaded', function(){ if (document.getElementById('invoices-tbody')) loadInvoices(); if (document.getElementById('payments-tbody')) loadPayments(); });

    // Delegated event handlers so buttons in dynamically-rendered rows work without inline onclick
    document.addEventListener('DOMContentLoaded', function(){
        const invTbody = document.getElementById('invoices-tbody');
        if (invTbody) {
            invTbody.addEventListener('click', function(e){
                const btn = e.target.closest('button[data-action]');
                if (!btn) return;
                const action = btn.getAttribute('data-action');
                const id = btn.getAttribute('data-id');
                if (!action || !id) return;
                if (action === 'view') { viewInvoice(Number(id)); }
                else if (action === 'delete') { deleteInvoice(Number(id)); }
            });
        }

        const payTbody = document.getElementById('payments-tbody');
        if (payTbody) {
            payTbody.addEventListener('click', function(e){
                const btn = e.target.closest('button[data-action]');
                if (!btn) return;
                const action = btn.getAttribute('data-action');
                const id = btn.getAttribute('data-id');
                if (!action || !id) return;
                if (action === 'view') { viewPayment(Number(id)); }
                else if (action === 'edit') { editPayment(Number(id)); }
                else if (action === 'delete') { deletePayment(Number(id)); }
            });
        }
        const trackTbody = document.getElementById('tracking-tbody');
        if (trackTbody) {
            trackTbody.addEventListener('click', function(e){
                const btn = e.target.closest('button[data-action]');
                if (!btn) return;
                const action = btn.getAttribute('data-action');
                const id = btn.getAttribute('data-id');
                if (!action || !id) return;
                if (action === 'view') { viewInvoice(Number(id)); }
            });
        }
        // Document-level fallback delegation for tracking Details buttons so they work
        // even if the tbody is replaced dynamically or not present at DOMContentLoaded.
        document.addEventListener('click', function(e){
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const action = btn.getAttribute('data-action');
            if (action !== 'view') return;
            // ensure this button is inside the tracking table
            const tr = btn.closest('tr');
            if (!tr) return;
            const table = tr.closest('table');
            if (!table) return;
            // look for the tracking tbody ancestor or id on the table container
            const trackingTbody = document.getElementById('tracking-tbody');
            if (trackingTbody && trackingTbody.contains(tr)) {
                const id = btn.getAttribute('data-id');
                if (id) viewInvoice(Number(id));
            }
        });
    });

    const paymentsApi = '/api/payments';

    async function loadPayments() {
        const tbody = document.getElementById('payments-tbody');
        try {
            const res = await fetch(paymentsApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) { let text = await res.text().catch(()=>null); throw new Error('Failed to load payments: ' + (text || res.statusText || res.status)); }
            const json = await res.json();
            const list = Array.isArray(json) ? json : (json.data || []);
            const rows = (list || []).map(p => {
                return `<tr data-id="${p.id}"><td>${p.payment_number||p.id}</td><td>${p.invoice?.invoice_number||''}</td><td>${p.invoice?.customer?.company_name||p.invoice?.customer?.contact_person||''}</td><td><strong>DZD ${p.amount ?? 0}</strong></td><td>${p.method || ''}</td><td><button class="btn btn-secondary" data-action="view" data-id="${p.id}" style="padding:3px 6px;font-size:0.7rem;">View</button> <button class="btn btn-primary" data-action="edit" data-id="${p.id}" style="padding:3px 6px;font-size:0.7rem;">Edit</button> <button class="btn btn-danger" data-action="delete" data-id="${p.id}" style="padding:3px 6px;font-size:0.7rem;">Delete</button></td></tr>`;
            }).join('');
            tbody.innerHTML = rows || '<tr><td colspan="6">No payments found</td></tr>';
        } catch (e) { tbody.innerHTML = '<tr><td colspan="6">Error loading payments</td></tr>'; console.error(e); }
        // refresh payment stats
        if (typeof loadPaymentStats === 'function') try { loadPaymentStats(); } catch (e) {}
    }

    async function viewPayment(id) {
        showModal(); document.getElementById('modal-title').textContent = 'Payment #' + id; document.getElementById('modal-body').innerHTML = '<div style="padding:12px;">Loading...</div>';
        try {
            const res = await fetch(`/api/payments/${id}`, { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to load payment');
            const p = await res.json();
            document.getElementById('modal-body').innerHTML = `<div><strong>Payment Number:</strong> ${p.payment_number}</div><div><strong>Invoice:</strong> ${p.invoice?.invoice_number||''}</div><div><strong>Amount:</strong> DZD ${p.amount}</div><div style="margin-top:8px;"><strong>Notes:</strong><div>${p.notes||''}</div></div><div style="margin-top:12px;"><button class="btn btn-secondary" onclick="hideModal()">Close</button></div>`;
        } catch (e) { document.getElementById('modal-body').innerHTML = '<div>Error loading payment</div>'; }
    }

    async function editPayment(id) {
        showModal(); document.getElementById('modal-title').textContent = 'Edit Payment #' + id; document.getElementById('modal-body').innerHTML = '<div style="padding:12px;">Loading...</div>';
        try {
            const [pRes, invRes] = await Promise.all([
                fetch(`/api/payments/${id}`, { credentials: 'same-origin', headers: {'Accept':'application/json'} }),
                fetch('/api/invoices?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} })
            ]);
            if (!pRes.ok) throw new Error('Failed to load payment');
            const p = await pRes.json();
            const invJson = invRes.ok ? await invRes.json().catch(()=>null) : null; const invList = Array.isArray(invJson) ? invJson : (invJson?.data || []);
            document.getElementById('modal-body').innerHTML = `
                <form id="edit-payment-form">
                    <div class="form-group"><label class="form-label">Invoice</label><select name="invoice_id" class="form-input">${invList.map(i=>`<option value="${i.id}" ${i.id==p.invoice_id?'selected':''}>${i.invoice_number} - Balance: ${i.balance ?? 0}</option>`).join('')}</select></div>
                    <div class="form-group"><label class="form-label">Payment Date</label><input name="payment_date" type="date" class="form-input" value="${p.payment_date ? p.payment_date.split('T')[0] : ''}" /></div>
                    <div class="form-group"><label class="form-label">Amount</label><input name="amount" type="number" step="0.01" class="form-input" value="${p.amount}" /></div>
                    <div class="form-group"><label class="form-label">Method</label><select name="method" class="form-input"><option value="bank_transfer" ${p.method=='bank_transfer'?'selected':''}>Bank Transfer</option><option value="cash" ${p.method=='cash'?'selected':''}>Cash</option><option value="credit_card" ${p.method=='credit_card'?'selected':''}>Credit Card</option><option value="wire_transfer" ${p.method=='wire_transfer'?'selected':''}>Wire Transfer</option></select></div>
                    <div class="form-group"><label class="form-label">Transaction Reference</label><input name="transaction_reference" class="form-input" value="${p.transaction_reference||''}" /></div>
                    <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-input" rows="3">${p.notes||''}</textarea></div>
                    <div style="display:flex; gap:12px; margin-top:12px;"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-secondary" type="button" onclick="hideModal()">Cancel</button></div>
                </form>
            `;
            const form = document.getElementById('edit-payment-form');
            form.addEventListener('submit', async function(e){ e.preventDefault(); const body = Object.fromEntries(new FormData(form).entries()); const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); const res = await fetch(`/api/payments/${id}`, { method: 'PUT', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token}, body: JSON.stringify(body)}); if (!res.ok) { const err = await res.json().catch(()=>null); showToaster('error','Error', err?.message || 'Update failed'); return; } hideModal(); showToaster('success','Updated','Payment updated'); loadPayments(); });
        } catch (e) { document.getElementById('modal-body').innerHTML = '<div>Error preparing edit form</div>'; }
    }

    async function deletePayment(id) {
        if (!(await showConfirm('Delete this payment?'))) return;
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/api/payments/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token}});
            if (!res.ok) throw new Error('Delete failed');
            showToaster('success','Deleted','Payment deleted');
            loadPayments();
        } catch (e) { showToaster('error','Error', e.message || 'Delete failed'); }
    }

    async function loadInvoicingStats() {
        try {
            const res = await fetch('/api/invoices/statistics', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) return;
            const s = await res.json();
            document.getElementById('invoices-outstanding-amount').textContent = `DZD ${s.outstanding ?? 0}`;
            document.getElementById('invoices-overdue-amount').textContent = `DZD ${s.overdue ?? 0}`;
            document.getElementById('invoices-paid-amount').textContent = `DZD ${s.paid_this_month ?? 0}`;
            document.getElementById('invoices-overdue-count').textContent = `${s.overdue_count ?? 0} overdue invoices`;
            // Count outstanding not provided by API; optional: fetch counts separately or leave as-is
        } catch (e) { console.error('loadInvoicingStats failed', e); }
    }

    async function loadPaymentStats() {
        try {
            const res = await fetch('/api/payments/statistics', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) return;
            const s = await res.json();
            // Placeholders: you can extend the UI with these stats
            // e.g., set a small badge or update other DOM nodes if present
            console.log('payment stats', s);
        } catch (e) { console.error('loadPaymentStats failed', e); }
    }

    // Tracking loader: computes payment-rate, avg payment time, collection rate and breakdown
    async function loadTracking() {
        const tbody = document.getElementById('tracking-tbody');
        const filter = document.getElementById('tracking-filter') ? document.getElementById('tracking-filter').value : 'all';
        try {
            const res = await fetch('/api/invoices?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to load invoices for tracking');
            const json = await res.json();
            const invoices = Array.isArray(json) ? json : (json.data || []);

            // filter by status categories
            let filtered = invoices;
            if (filter !== 'all') {
                if (filter === 'unpaid') {
                    filtered = invoices.filter(i => (i.balance || 0) > 0 && !(i.status && i.status === 'paid'));
                } else if (filter === 'partial') {
                    filtered = invoices.filter(i => { const total = Number(i.total_amount || 0); const bal = Number(i.balance || 0); return total>0 && bal>0 && bal < total; });
                } else if (filter === 'paid') {
                    filtered = invoices.filter(i => (i.status === 'paid') || (Number(i.balance || 0) === 0 && Number(i.total_amount || 0) > 0));
                } else if (filter === 'overdue') {
                    filtered = invoices.filter(i => i.status === 'overdue' || (i.due_date && new Date(i.due_date).getTime() < Date.now() && Number(i.balance || 0) > 0));
                }
            }

            // Compute metrics
            let totalInvoices = filtered.length;
            let collectedAmount = 0; let totalAmount = 0; let onTimeCount = 0; let totalPaymentDays = 0; let paymentCount = 0; let overdueCount = 0;
            const breakdown = { paid: 0, partial: 0, unpaid: 0, overdue: 0 };
            let amounts = { paid: 0, partial: 0, unpaid: 0, overdue: 0 };

            filtered.forEach(inv => {
                const subtotal = Number(inv.total_amount || 0); totalAmount += subtotal;
                const paid = Number((inv.total_amount || 0) - (inv.balance || 0)); collectedAmount += paid;
                const pct = subtotal ? Math.min(100, Math.round((paid / subtotal) * 100)) : 0;
                if (inv.status === 'paid' || pct === 100) { breakdown.paid++; amounts.paid += subtotal; }
                else if (inv.status === 'overdue') { breakdown.overdue++; overdueCount++; amounts.overdue += subtotal; }
                else if (pct > 0) { breakdown.partial++; amounts.partial += subtotal; }
                else { breakdown.unpaid++; amounts.unpaid += subtotal; }

                // average payment time: if invoice has payments array with payment_date, compute days
                if (inv.payments && inv.payments.length) {
                    inv.payments.forEach(p => {
                        if (p.payment_date && inv.issue_date) {
                            const d1 = new Date(inv.issue_date).getTime(); const d2 = new Date(p.payment_date).getTime(); if (!isNaN(d1) && !isNaN(d2)) { totalPaymentDays += Math.max(0, (d2 - d1) / (1000*60*60*24)); paymentCount++; if ((d2 - d1) <= (30*24*60*60*1000)) onTimeCount++; }
                        }
                    });
                }
            });

            const paymentRate = paymentCount ? Math.round((onTimeCount / paymentCount) * 100) : 0;
            const avgPaymentTime = paymentCount ? Math.round((totalPaymentDays / paymentCount)) : 0;
            const collectionRate = totalAmount ? Math.round((collectedAmount / totalAmount) * 100) : 0;

            // update cards
            document.getElementById('track-payment-rate').textContent = paymentRate + '%';
            document.getElementById('track-payment-rate-bar').style.width = paymentRate + '%';
            document.getElementById('track-avg-time').textContent = avgPaymentTime + ' days';
            document.getElementById('track-collection-rate').textContent = collectionRate + '%';

            // breakdown boxes
            const bd = document.getElementById('track-breakdown'); bd.innerHTML = '';
            const createBox = (title, count, amount, color) => `<div style="flex:1; min-width:160px; padding:12px; border-radius:8px; border:1px solid #e5e5e5; background:#fff"><div style="font-weight:600;color:#374151">${title}</div><div style="font-size:1.1rem;font-weight:700;margin-top:8px;color:${color}">${amount}</div><div style="font-size:0.8rem;color:#6b7280;margin-top:6px">${count} invoices</div></div>`;
            bd.innerHTML += createBox('Fully Paid', breakdown.paid, `DZD ${Math.round(amounts.paid)}`, '#10b981');
            bd.innerHTML += createBox('Partially Paid', breakdown.partial, `DZD ${Math.round(amounts.partial)}`, '#f59e0b');
            bd.innerHTML += createBox('Unpaid', breakdown.unpaid, `DZD ${Math.round(amounts.unpaid)}`, '#f97316');
            bd.innerHTML += createBox('Overdue', breakdown.overdue, `DZD ${Math.round(amounts.overdue)}`, '#ef4444');

            // tracking table rows
            const rows = filtered.map(inv => {
                const paid = Number((inv.total_amount || 0) - (inv.balance || 0));
                const pct = inv.total_amount ? Math.round((paid / inv.total_amount) * 100) : 0;
                const days = inv.due_date ? Math.round((Date.now() - new Date(inv.due_date).getTime()) / (1000*60*60*24)) : (inv.issue_date ? Math.round((Date.now() - new Date(inv.issue_date).getTime()) / (1000*60*60*24)) : 0);
                const statusBadge = inv.status === 'paid' ? '<span class="badge badge-success">Paid</span>' : (inv.status === 'overdue' ? '<span class="badge badge-danger">Overdue</span>' : '<span class="badge badge-warning">Unpaid</span>');
                return `<tr data-id="${inv.id}"><td><strong>${inv.invoice_number || inv.id}</strong></td><td>${inv.customer?.company_name||inv.customer?.contact_person||''}</td><td>DZD ${inv.total_amount ?? 0}</td><td>DZD ${paid}</td><td style="color:#f57c00;font-weight:600;">DZD ${inv.balance ?? 0}</td><td>${pct}%</td><td>${Math.abs(days)} days</td><td>${statusBadge}</td><td><button class="btn btn-secondary" data-action="view" data-id="${inv.id}" style="padding:3px 6px;font-size:0.7rem;">Details</button></td></tr>`;
            }).join('');
            tbody.innerHTML = rows || '<tr><td colspan="9">No invoices found</td></tr>';

        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="9">Error loading tracking data</td></tr>';
            console.error(e);
        }
    }

    // wire tracking filter and initial load
    document.addEventListener('DOMContentLoaded', function(){ const f = document.getElementById('tracking-filter'); if (f) { f.addEventListener('change', loadTracking); }
        // if the tracking tab is currently active, load it immediately
        const trackingTab = document.getElementById('invoicing-tracking'); if (trackingTab && trackingTab.classList.contains('active')) { if (typeof loadTracking === 'function') loadTracking(); }
    });

    // --- Export helpers for Invoicing module ---
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

    function _formatDate(v) {
        if (!v) return '';
        try {
            const d = new Date(v);
            if (isNaN(d.getTime())) return String(v);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return `${d.getDate().toString().padStart(2,'0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
        } catch (e) { return String(v); }
    }

    function _downloadCSV(filename, csv) {
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 5000);
    }

    async function exportInvoices() {
        try {
            const res = await fetch(invoicesApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch invoices');
            const json = await res.json(); const list = Array.isArray(json) ? json : (json.data || []);
            if (!list.length) return showToaster('info','No data','No invoices to export');
            const header = ['Invoice #','Customer','Total Amount','Balance','Status','Issue Date','Due Date','Payments Count'];
            const rows = list.map(inv => [inv.invoice_number||inv.id, (inv.customer?.company_name||inv.customer?.contact_person||''), Number(inv.total_amount||0).toFixed(2), Number(inv.balance||0).toFixed(2), inv.status||'', _formatDate(inv.issue_date), _formatDate(inv.due_date), (inv.payments?inv.payments.length:0)]);
            const csv = _arrayToCSV(header, rows);
            const ts = new Date().toISOString().slice(0,10).replace(/-/g,'');
            _downloadCSV(`invoices-${ts}.csv`, csv);
            showToaster('success','Exported', `Exported ${rows.length} invoices`);
        } catch (err) { console.error('Invoices export failed', err); showToaster('error','Export failed', err.message || 'Failed to export invoices'); }
    }

    async function exportPayments() {
        try {
            const res = await fetch(paymentsApi + '?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch payments');
            const json = await res.json(); const list = Array.isArray(json) ? json : (json.data || []);
            if (!list.length) return showToaster('info','No data','No payments to export');
            const header = ['Payment #','Invoice #','Customer','Amount','Method','Payment Date','Reference'];
            const rows = list.map(p => [p.payment_number||p.id, (p.invoice?.invoice_number||''), (p.invoice?.customer?.company_name||p.invoice?.customer?.contact_person||''), Number(p.amount||0).toFixed(2), p.method||'', _formatDate(p.payment_date), p.transaction_reference||'']);
            const csv = _arrayToCSV(header, rows);
            const ts = new Date().toISOString().slice(0,10).replace(/-/g,'');
            _downloadCSV(`payments-${ts}.csv`, csv);
            showToaster('success','Exported', `Exported ${rows.length} payments`);
        } catch (err) { console.error('Payments export failed', err); showToaster('error','Export failed', err.message || 'Failed to export payments'); }
    }

    async function exportTracking() {
        try {
            // Export the same set used by loadTracking (all invoices, with details)
            const res = await fetch('/api/invoices?per_page=1000', { credentials: 'same-origin', headers: {'Accept':'application/json'} });
            if (!res.ok) throw new Error('Failed to fetch invoices for tracking');
            const json = await res.json(); const list = Array.isArray(json) ? json : (json.data || []);
            if (!list.length) return showToaster('info','No data','No invoices to export');
            const header = ['Invoice #','Customer','Total Amount','Amount Paid','Balance','% Paid','Issue Date','Due Date','Status','Payments Details'];
            const rows = list.map(inv => {
                const total = Number(inv.total_amount||0);
                const paid = total - Number(inv.balance||0);
                const pct = total ? Math.round((paid/total)*100) : 0;
                const payments = (inv.payments||[]).map(p => `${p.payment_number||p.id}:${(p.amount||0)}`).join('|');
                return [inv.invoice_number||inv.id, (inv.customer?.company_name||inv.customer?.contact_person||''), total.toFixed(2), paid.toFixed(2), Number(inv.balance||0).toFixed(2), pct + '%', _formatDate(inv.issue_date), _formatDate(inv.due_date), inv.status||'', payments];
            });
            const csv = _arrayToCSV(header, rows);
            const ts = new Date().toISOString().slice(0,10).replace(/-/g,'');
            _downloadCSV(`tracking-invoices-${ts}.csv`, csv);
            showToaster('success','Exported', `Exported ${rows.length} invoices`);
        } catch (err) { console.error('Tracking export failed', err); showToaster('error','Export failed', err.message || 'Failed to export tracking data'); }
    }
</script>
@endpush

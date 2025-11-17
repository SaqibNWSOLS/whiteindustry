@extends('layouts.app')

@section('title', 'CRM')
@section('page_title', 'CRM')

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <button class="tab-button active" onclick="showTab('crm', 'customers', this)">Customers</button>
            <button class="tab-button" onclick="showTab('crm', 'leads', this)">Leads</button>
            <button class="tab-button" onclick="showTab('crm', 'quotes', this)">Quotes</button>
        </div>
    </div>

    <div id="crm-customers" class="tab-content active">
        <div class="module-header">
            <button class="btn btn-primary" id="customers-add-btn"><i class="ti ti-user-plus"></i> Add Customer</button>
            <button class="btn btn-secondary" onclick="exportCRM('customers')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" id="customers-search" class="search-input" placeholder="Search customers...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Customer Database</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="crm-customers-tbody">
                    {{-- Dynamic customer rows will be injected here --}}
                </tbody>
            </table>
        </div>
    </div>

    <div id="crm-leads" class="tab-content">
        <div class="module-header">
            <button class="btn btn-primary" id="leads-add-btn"><i class="ti ti-user-plus"></i> Add Lead</button>
            <button class="btn btn-secondary" onclick="exportCRM('leads')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" id="leads-search" class="search-input" placeholder="Search leads...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Sales Leads</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Lead ID</th>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="crm-leads-tbody">
                    {{-- Dynamic lead rows will be injected here --}}
                </tbody>
            </table>
        </div>
    </div>

    <div id="crm-quotes" class="tab-content">
        <div class="module-header">
            <a href="{{ route('quotes.create') }}" class="btn btn-primary"   ><i class="ti ti-file-plus"></i> Create Quote</a>
            <button class="btn btn-secondary" onclick="exportCRM('quotes')" style="margin-left:8px"><i class="ti ti-download"></i> Export</button>
            <input type="search" id="quotes-search" class="search-input" placeholder="Search quotes...">
        </div>
        <div class="table-container">
            <div class="table-header"><h3>Sales Quotes</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Quote ID</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="crm-quotes-tbody">
                    {{-- Dynamic quote rows will be injected here --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/quote.js') }}"></script>

<script>

    document.addEventListener('DOMContentLoaded', function() {
    // Load quotes if quotes tab is active
    const activeTab = document.querySelector('#crm-quotes.tab-content.active');
    if (activeTab) {
        quoteManager.loadQuotes();
    }
});
// Simple tab toggling for CRM module and customers loading
function showTab(module, tab, btn) {
    // deactivate sibling buttons
    const nav = btn.closest('.tab-nav');
    nav.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // hide all tab-content in this module
    document.querySelectorAll(`#${module}-customers, #${module}-leads, #${module}-quotes`).forEach(el => el.classList.remove('active'));
    const target = document.getElementById(`${module}-${tab}`);
    if (target) target.classList.add('active');

    // load data for the activated tab
    if (tab === 'customers') loadCustomers();
    if (tab === 'leads') loadLeads();
    if (tab === 'quotes') quoteManager.loadQuotes();
}

async function loadCustomers(q = '') {
    const tbody = document.getElementById('crm-customers-tbody');
    tbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';
    try {
        const url = '/api/customers' + (q ? ('?q=' + encodeURIComponent(q)) : '');
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Failed to fetch customers');
        const data = await res.json();
        const items = data.data || data;
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="6">No customers found</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(c => `
            <tr>
                <td>${c.customer_code ?? c.customer_id}</td>
                <td>${escapeHtml(c.company_name ?? '')}</td>
                <td>${escapeHtml(c.contact_person ?? '')}</td>
                <td>${escapeHtml(c.email ?? '')}</td>
                <td><span class="badge ${c.status === 'active' ? 'badge-success' : 'badge-secondary'}">${c.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editCustomer(${c.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${c.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="6">Error loading customers: ${escapeHtml(err.message)}</td></tr>`;
    }
}

function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/[&<>\"]/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];
    });
}

async function deleteCustomer(id) {
    if (!(await showConfirm('Delete this customer?'))) return;
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch(`/api/customers/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token} });
        if (!res.ok) throw new Error('Delete failed');
        loadCustomers();
    } catch (err) {
        showToaster('error', 'Error', 'Failed to delete customer: ' + err.message);
    }
}

function createItem(type) {
    // Use layout modal: #modal-overlay, #modal-title, #modal-body
    const modalOverlay = document.getElementById('modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    modalTitle.textContent = `Create ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        if (type === 'customer') {
        modalBody.innerHTML = `
            <form id="customer-form">
                <div class="form-group"><label class="form-label">Type</label>
                    <select id="customer-type" name="type" class="form-input">
                        <option value="business">Business</option>
                        <option value="person">Person</option>
                    </select>
                </div>
                <div id="business-fields">
                    <div class="form-group"><label class="form-label">Company Name</label>
                        <input name="company_name" class="form-input" />
                    </div>
                    <div class="form-group"><label class="form-label">Industry Type</label>
                        <select name="industry_type" class="form-input">
                            <option value="">Select industry</option>
                            <option value="Cosmetics & Beauty">Cosmetics & Beauty</option>
                            <option value="Pharmaceuticals">Pharmaceuticals</option>
                            <option value="Dietary Supplements">Dietary Supplements</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Tax ID</label>
                        <input name="tax_id" class="form-input" />
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Contact Person</label>
                    <input name="contact_person" class="form-input" required />
                </div>
                <div class="form-group"><label class="form-label">Email</label>
                    <input name="email" type="email" class="form-input" required />
                </div>
                <div class="form-group"><label class="form-label">Phone</label>
                    <input name="phone" class="form-input" required />
                </div>
                <div class="form-group"><label class="form-label">Address</label>
                    <input name="address" class="form-input" />
                </div>
                <div class="form-group"><label class="form-label">City</label>
                    <input name="city" class="form-input" />
                </div>
                <div class="form-group"><label class="form-label">Postal Code</label>
                    <input name="postal_code" class="form-input" />
                </div>
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div style="display:flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
                </div>
            </form>
        `;
        showModal();
        // attach submit handler
        const form = document.getElementById('customer-form');
        const typeSelect = document.getElementById('customer-type');
        const businessFields = document.getElementById('business-fields');

        function toggleBusinessFields() {
            if (!typeSelect) return;
            if (typeSelect.value === 'business') {
                businessFields.style.display = '';
                // ensure required company_name when business
                const company = form.company_name;
                if (company) company.required = true;
            } else {
                businessFields.style.display = 'none';
                // clear and remove required
                ['company_name','industry_type','tax_id'].forEach(n => { if (form[n]) { form[n].value = ''; form[n].required = false; } });
            }
        }

        // initial toggle
        toggleBusinessFields();
        // wire change
        typeSelect.addEventListener('change', toggleBusinessFields);

        form.addEventListener('submit', submitCustomerForm);
    }
    // leads/quotes will be implemented similarly later
}

async function submitCustomerForm(e) {
    e.preventDefault();
    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch('/api/customers', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token},
            body: JSON.stringify(data)
        });
        if (!res.ok) {
            const errBody = await res.json().catch(() => null);
            throw new Error(errBody?.message || 'Create failed');
        }
        hideModal();
        loadCustomers();
        showToaster('success', 'Customer created', 'Customer was created successfully.');
    } catch (err) {
        showToaster('error', 'Error', 'Failed to create customer: ' + err.message);
    }
}

async function editCustomer(id) {
    try {
        const res = await fetch(`/api/customers/${id}`, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Failed to load customer');
        const customer = await res.json();
        // Build modal form and pre-fill
        createItem('customer');
        const form = document.getElementById('customer-form');
        form.company_name.value = customer.company_name || '';
        form.industry_type.value = customer.industry_type || '';
        form.tax_id.value = customer.tax_id || '';
        form.contact_person.value = customer.contact_person || '';
        form.email.value = customer.email || '';
        form.phone.value = customer.phone || '';
        form.address.value = customer.address || '';
        form.city.value = customer.city || '';
        form.postal_code.value = customer.postal_code || '';
        form.status.value = customer.status || 'active';
        form.type.value = customer.type || 'business';

        // If toggle function exists, trigger it to show/hide business fields
        try {
            const typeSelect = document.getElementById('customer-type');
            if (typeSelect) {
                typeSelect.value = customer.type || 'business';
                // dispatch change so handlers update required/visibility
                typeSelect.dispatchEvent(new Event('change'));
            }
        } catch (e) {
            // ignore
        }

        // replace submit handler to perform PUT
        form.removeEventListener('submit', submitCustomerForm);
        const updateHandler = async function (ev) {
            ev.preventDefault();
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const body = Object.fromEntries(new FormData(form).entries());
            const r = await fetch(`/api/customers/${id}`, { method: 'PUT', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token}, body: JSON.stringify(body) });
            if (!r.ok) {
                const err = await r.json().catch(() => null);
                showToaster('error', 'Error', 'Update failed: ' + (err?.message || r.statusText));
                return;
            }
            hideModal();
            loadCustomers();
            showToaster('success', 'Customer updated', 'Customer was updated successfully.');
        };
        form.addEventListener('submit', updateHandler);
    } catch (err) {
        showToaster('error', 'Error', 'Failed to load customer: ' + err.message);
    }
}

// Placeholder lead/quote loaders (will be implemented later)
function loadLeads() {
    const tbody = document.getElementById('crm-leads-tbody');
    tbody.innerHTML = '<tr><td colspan="5">Leads loading not implemented yet</td></tr>';
}


// --- Leads implementation ---
async function loadLeads(q = '') {
    const tbody = document.getElementById('crm-leads-tbody');
    tbody.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';
    try {
        const url = '/api/leads' + (q ? ('?q=' + encodeURIComponent(q)) : '');
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Failed to fetch leads');
        const data = await res.json();
        const items = data.data || data;
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5">No leads found</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(l => `
            <tr>
                <td>${l.lead_id ?? l.id}</td>
                <td>${escapeHtml(l.company_name || '')}</td>
                <td>${escapeHtml(l.contact_person || '')}</td>
                <td><span class="badge ${l.status === 'qualified' ? 'badge-warning' : 'badge-secondary'}">${l.status || ''}</span></td>
                <td>${escapeHtml(l.estimated_value || '')}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editLead(${l.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteLead(${l.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="5">Error loading leads: ${escapeHtml(err.message)}</td></tr>`;
    }
}

function createLeadModal() {
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    modalTitle.textContent = 'Create Lead';
    modalBody.innerHTML = `
        <form id="lead-form">
            <div class="form-group"><label class="form-label">Source</label>
                <select name="source" class="form-input"><option value="website">Website</option><option value="referral">Referral</option><option value="trade_show">Trade Show</option><option value="cold_call">Cold Call</option><option value="social_media">Social Media</option></select>
                </div>
            <div class="form-group"><label class="form-label">Company Name</label><input name="company_name" class="form-input" /></div>
            <div class="form-group"><label class="form-label">Contact Person</label><input name="contact_person" class="form-input" /></div>
            <div class="form-group"><label class="form-label">Email</label><input name="email" class="form-input" type="email" /></div>
            <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-input" /></div>
            <div class="form-group"><label class="form-label">Status</label>
                <select name="status" class="form-input"><option value="new">New</option><option value="contacted">Contacted</option><option value="qualified">Qualified</option><option value="converted">Converted</option><option value="proposal">Proposal</option><option value="lost">Lost</option></select>
            </div>
            <div class="form-group"><label class="form-label">Estimated Value</label><input name="estimated_value" class="form-input" type="number" step="0.01" /></div>
            <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-input"></textarea></div>
            <div style="display:flex; gap: 12px; margin-top: 12px;"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-secondary" type="button" onclick="hideModal()">Cancel</button></div>
        </form>
    `;
    showModal();
    const form = document.getElementById('lead-form');
    form.addEventListener('submit', submitLeadForm);
}

async function submitLeadForm(e) {
    e.preventDefault();
    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch('/api/leads', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token}, body: JSON.stringify(data) });
        if (!res.ok) {
            const err = await res.json().catch(() => null);
            throw new Error(err?.message || 'Create failed');
        }
        hideModal();
        loadLeads();
    } catch (err) {
        showToaster('error', 'Error', 'Failed to create lead: ' + err.message);
    }
}

async function editLead(id) {
    try {
        const res = await fetch(`/api/leads/${id}`, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Failed to load lead');
        const l = await res.json();
        createLeadModal();
        const form = document.getElementById('lead-form');
        form.source.value = l.source || '';
        form.company_name.value = l.company_name || '';
        form.contact_person.value = l.contact_person || '';
        form.email.value = l.email || '';
        form.phone.value = l.phone || '';
        form.status.value = l.status || 'new';
        form.estimated_value.value = l.estimated_value || '';
        form.notes.value = l.notes || '';

        form.removeEventListener('submit', submitLeadForm);
        form.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const body = Object.fromEntries(new FormData(form).entries());
            const r = await fetch(`/api/leads/${id}`, { method: 'PUT', credentials: 'same-origin', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token}, body: JSON.stringify(body) });
            if (!r.ok) { const err = await r.json().catch(()=>null); showToaster('error','Error','Update failed: ' + (err?.message || r.statusText)); return; }
                hideModal();
                loadLeads();
                showToaster('success','Lead updated','Lead was updated successfully.');
        });
    } catch (err) {
            showToaster('error','Error','Failed to load lead: ' + err.message);
    }
}

async function deleteLead(id) {
    if (!(await showConfirm('Delete this lead?'))) return;
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch(`/api/leads/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: {'X-CSRF-TOKEN': token} });
        if (!res.ok) throw new Error('Delete failed');
        loadLeads();
    } catch (err) {
        showToaster('error','Error','Failed to delete lead: ' + err.message);
    }
}




// On initial load, ensure the customers tab is loaded
document.addEventListener('DOMContentLoaded', function () {
    // load customers if customers tab is active
    const activeBtn = document.querySelector('.tab-nav .tab-button.active');
    if (activeBtn) {
        const onclick = activeBtn.getAttribute('onclick');
        // If initial markup used showTab, trigger manual loading
        if (activeBtn.textContent.trim().toLowerCase() === 'customers') loadCustomers();
    }
    // attach add button handler
    const addBtn = document.getElementById('customers-add-btn');
    if (addBtn) {
        addBtn.removeEventListener('click', () => createItem('customer'));
        addBtn.addEventListener('click', () => createItem('customer'));
    }

    // attach search handler with debounce
    const search = document.getElementById('customers-search');
    if (search) {
        let debounce;
        search.addEventListener('input', function (e) {
            clearTimeout(debounce);
            const q = e.target.value.trim();
            debounce = setTimeout(() => loadCustomers(q), 350);
        });
    }

    // Leads add & search wiring
    const leadsAdd = document.getElementById('leads-add-btn');
    if (leadsAdd) {
        leadsAdd.addEventListener('click', function () { createLeadModal(); });
    }
    const leadsSearch = document.getElementById('leads-search');
    if (leadsSearch) {
        let ld;
        leadsSearch.addEventListener('input', function (e) {
            clearTimeout(ld);
            const q = e.target.value.trim();
            ld = setTimeout(() => loadLeads(q), 350);
        });
    }
    // Quotes add & search wiring
    const quotesAdd = document.getElementById('quotes-add-btn');
    if (quotesAdd) {
        quotesAdd.addEventListener('click', function () { createQuoteModal(); });
    }
    const quotesSearch = document.getElementById('quotes-search');
    if (quotesSearch) {
        let qd;
        quotesSearch.addEventListener('input', function (e) {
            clearTimeout(qd);
            const q = e.target.value.trim();
            qd = setTimeout(() => loadQuotes(q), 350);
        });
    }
});

// --- Export helpers for CRM module ---
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
    const a = document.createElement('a');
    a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 5000);
}

async function exportCRM(tab) {
    try {
        let header = [];
        let rows = [];
        if (tab === 'customers') {
            const res = await fetch('/api/customers?per_page=1000', { credentials: 'same-origin' }); if (!res.ok) throw new Error('Failed to fetch customers');
            const json = await res.json(); const list = json.data || json || [];
            if (!list.length) return showToaster('info','No data','No customers to export');
            header = ['Customer ID','Company Name','Contact Person','Email','Status'];
            rows = list.map(c => [c.customer_code||c.customer_id||c.id, c.company_name||'', c.contact_person||'', c.email||'', c.status||'']);
        } else if (tab === 'leads') {
            const res = await fetch('/api/leads?per_page=1000', { credentials: 'same-origin' }); if (!res.ok) throw new Error('Failed to fetch leads');
            const json = await res.json(); const list = json.data || json || [];
            if (!list.length) return showToaster('info','No data','No leads to export');
            header = ['Lead ID','Company','Contact','Status','Estimated Value'];
            rows = list.map(l => [l.lead_id||l.id, l.company_name||'', l.contact_person||'', l.status||'', Number(l.estimated_value||0).toFixed(2)]);
        } else if (tab === 'quotes') {
            const res = await fetch('/api/quotes?per_page=1000', { credentials: 'same-origin' }); if (!res.ok) throw new Error('Failed to fetch quotes');
            const json = await res.json(); const list = json.data || json || [];
            if (!list.length) return showToaster('info','No data','No quotes to export');
            header = ['Quote ID','Customer','Product','Total Value','Status'];
            rows = list.map(q => [q.quote_number||q.id, (q.customer?.company_name||''), (q.product?.name||''), Number(q.total_amount||q.total_value||0).toFixed(2), q.status||'']);
        } else {
            return showToaster('error','Export failed','Unknown export type');
        }

        const csv = arrayToCSV(header, rows);
        const now = new Date(); const ts = now.toISOString().slice(0,10).replace(/-/g,'');
        downloadCSV(`crm-${tab}-${ts}.csv`, csv);
        showToaster('success','Exported', `Exported ${rows.length} rows`);
    } catch (err) {
        console.error('CRM export failed', err);
        showToaster('error','Export failed', err.message || 'Failed to export data');
    }
}
</script>
@endpush

@extends('layouts.app')

@section('title', 'Production')
@section('page_title', 'Production')

@section('content')
<div class="content">
    <div class="module-header">
        <button class="btn btn-primary" onclick="createItem('production')"><i class="ti ti-plus"></i> New Production Order</button>
        <input type="search" class="search-input" placeholder="Search orders...">
    </div>
    <div class="table-container">
        <div class="table-header"><h3>Production Orders</h3></div>
        <table>
                <tbody id="production-orders-tbody">
                    <!-- populated by JS -->
                </tbody>
            <tbody>
                <tr>
                    <td>OF-2024-0156</td>
                    <td>Face Cream Base</td>

@push('scripts')
<script>
    const productionApi = '/api/production-orders';
    const productsApi = '/api/products';
    const customersApi = '/api/customers';

    async function loadProductionOrders(){
        try{
            const res = await fetch(productionApi + '?page=1', { credentials: 'same-origin' });
            if(!res.ok) throw new Error('Failed to load');
            const json = await res.json();
            const items = json.data || json;
            const tbody = document.getElementById('production-orders-tbody');
            tbody.innerHTML = '';
            items.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${p.of_number || ('OF-' + p.id)}</strong></td>
                    <td>${p.product ? (p.product.name || '') : ''}</td>
                    <td><span class="badge badge-info">${p.status || ''}</span></td>
                    <td>${p.due_date || ''}</td>
                    <td>
                        <button class="btn btn-secondary" style="padding:3px 6px;font-size:0.6rem;" onclick="editProduction(${p.id})">Edit</button>
                        <button class="btn btn-danger" style="padding:3px 6px;font-size:0.6rem;margin-left:6px;" onclick="deleteProduction(${p.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }catch(err){ console.error(err); }
    }

    async function openProductionForm(data = null){
        // load products and customers
        const [prodRes, custRes] = await Promise.all([
            fetch(productsApi + '?page=1', { credentials: 'same-origin' }),
            fetch(customersApi + '?page=1', { credentials: 'same-origin' })
        ]);
        const productsJson = prodRes.ok ? await prodRes.json().catch(()=>({data:[]})) : {data:[]};
        const customersJson = custRes.ok ? await custRes.json().catch(()=>({data:[]})) : {data:[]};
        const products = productsJson.data || productsJson;
        const customers = customersJson.data || customersJson;

        const title = data ? 'Edit Production Order' : 'Create New Order';
        let prodOptions = '<option value="">Select product</option>';
        products.forEach(p => { prodOptions += `<option value="${p.id}">${p.name || p.product_code}</option>`; });
        let custOptions = '<option value="">Select customer</option>';
        customers.forEach(c => { custOptions += `<option value="${c.id}">${c.company_name || c.name}</option>`; });

        const modalBody = `
            <div class="form-group"><label>Order Number</label><input id="po_of_number" class="form-input" value="${data ? (data.of_number||'') : ('OF-' + new Date().getFullYear() + '-' + Math.floor(Math.random()*900 +100))}"></div>
            <div class="form-group"><label>Customer</label><select id="po_customer">${custOptions}</select></div>
            <div class="form-group"><label>Product</label><select id="po_product">${prodOptions}</select></div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;"><div class="form-group"><label>Quantity</label><input id="po_quantity" type="number" class="form-input" value="${data ? (data.quantity||'') : 0}"></div><div class="form-group"><label>Unit</label><input id="po_unit" class="form-input" value="${data ? (data.unit||'L') : 'L'}"></div><div class="form-group"><label>Total Value (DZD)</label><input id="po_total" type="number" class="form-input" value="${data ? (data.total_value||'0') : '0.00'}"></div></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;"><div class="form-group"><label>Order Date</label><input id="po_start" type="date" class="form-input" value="${data && data.start_date ? data.start_date : ''}"></div><div class="form-group"><label>Delivery Date</label><input id="po_due" type="date" class="form-input" value="${data && data.due_date ? data.due_date : ''}"></div></div>
            <div class="form-group"><label>Priority</label><select id="po_priority" class="form-select"><option>Normal</option><option>High</option></select></div>
            <div class="form-group"><label>Special Instructions</label><textarea id="po_notes" class="form-input">${data ? (data.notes||'') : ''}</textarea></div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;"><button class="btn btn-primary" onclick="submitProduction(${data ? data.id : 'null'})">${data ? 'Save' : 'Create Order'}</button><button class="btn" onclick="hideModal()">Cancel</button></div>
        `;
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-body').innerHTML = modalBody;
        showModal();
        // set selected values if editing
        if(data){
            if(data.product_id) document.getElementById('po_product').value = data.product_id;
            if(data.order_id) document.getElementById('po_customer').value = data.order_id;
        }
    }

    async function submitProduction(id = null){
        const payload = {
            of_number: document.getElementById('po_of_number').value,
            order_id: document.getElementById('po_customer').value || null,
            product_id: document.getElementById('po_product').value,
            quantity: document.getElementById('po_quantity').value,
            unit: document.getElementById('po_unit').value,
            start_date: document.getElementById('po_start').value || null,
            due_date: document.getElementById('po_due').value || null,
            notes: document.getElementById('po_notes').value,
            // map priority -> production_line for now
            production_line: document.getElementById('po_priority').value,
        };
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const headers = { 'Content-Type': 'application/json' };
        if(tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
        try{
            let res;
            if(id){
                res = await fetch(productionApi + '/' + id, { method: 'PUT', credentials: 'same-origin', headers, body: JSON.stringify(payload) });
            } else {
                res = await fetch(productionApi, { method: 'POST', credentials: 'same-origin', headers, body: JSON.stringify(payload) });
            }
            if(!res.ok){ const err = await res.json().catch(()=>null); throw new Error((err && err.message) || res.statusText); }
            hideModal(); showToaster('success','Production','Saved'); loadProductionOrders();
        }catch(err){ console.error(err); showToaster('error','Production','Failed to save'); }
    }

    async function editProduction(id){
        try{
            const res = await fetch(productionApi + '/' + id, { credentials: 'same-origin' });
            if(!res.ok) return;
            const json = await res.json();
            openProductionForm(json.data || json);
        }catch(err){ console.error(err); }
    }

    async function deleteProduction(id){
    if(!(await showConfirm('Delete this production order?'))) return;
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const headers = {};
        if(tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
        try{
            const res = await fetch(productionApi + '/' + id, { method: 'DELETE', credentials: 'same-origin', headers });
            if(!res.ok) throw new Error('Delete failed');
            showToaster('success','Production','Deleted'); loadProductionOrders();
        }catch(err){ console.error(err); showToaster('error','Production','Delete failed'); }
    }

    document.addEventListener('DOMContentLoaded', function(){ loadProductionOrders(); });
</script>
@endpush
                    <td><span class="badge badge-info">Mixing</span></td>
                    <td>2024-03-15</td>
                    <td>
                        <button class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.6rem;" onclick="viewItem('OF-2024-0156')">View</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

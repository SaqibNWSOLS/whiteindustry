@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

                   <!-- Dashboard Module -->
                <div id="dashboard" class="module active">
                    <div class="dashboard-grid">
                        <div class="card">
                            <h3>
                                <i class="ti ti-building-factory-2"></i>
                                <span class="wi-highlight">Production Overview</span>
                            </h3>
                            <div class="status-card status-production">
                                <div>
                                    <strong>Active Orders</strong>
                                    <div id="production-active-desc" style="font-size: 0.77rem; color: #666;">— OF in progress</div>
                                </div>
                                <div id="production-active" style="font-size: 2rem; font-weight: bold; color: #000;">—</div>
                            </div>
                            <div class="status-card status-pending">
                                <div>
                                    <strong>Pending Launch</strong>
                                    <div id="production-pending-desc" style="font-size: 0.77rem; color: #666;">— orders scheduled</div>
                                </div>
                                <div id="production-pending" style="font-size: 2rem; font-weight: bold; color: #000;">—</div>
                            </div>
                            <div class="status-card status-completed">
                                <div>
                                    <strong>Completed Today</strong>
                                    <div id="production-completed-desc" style="font-size: 0.7rem; color: #666;">— orders finished</div>
                                </div>
                                <div id="production-completed" style="font-size: 2rem; font-weight: bold; color: #000;">—</div>
                            </div>
                        </div>

                        <div class="card">
                            <h3>
                                <i class="ti ti-packages"></i>
                                <span class="wi-highlight">Inventory Status</span>
                            </h3>
                            <div style="margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span>Raw Materials</span>
                                    <span id="inventory-raw-pct" style="color: #000;">—%</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="inventory-raw" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div style="margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span>Packaging</span>
                                    <span id="inventory-pack-pct" style="color: #000;">—%</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="inventory-pack" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span>Finished Products</span>
                                    <span id="inventory-fin-pct" style="color: #000;">—%</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="inventory-fin" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <h3>
                                <i class="ti ti-cash"></i>
                                <span class="wi-highlight">Sales & Revenue</span>
                            </h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="wi-border">
                                    <div style="font-size: 0.7rem; color: #666; margin-bottom: 4px;">Monthly Revenue</div>
                                    <div id="sales-monthly" style="font-size: 1.5rem; font-weight: bold; color: #000;">—</div>
                                </div>
                                <div class="wi-border">
                                    <div style="font-size: 0.7rem; color: #666; margin-bottom: 4px;">Active Quotes</div>
                                    <div id="sales-quotes" style="font-size: 1.5rem; font-weight: bold; color: #000;">—</div>
                                </div>
                                <div class="wi-border">
                                    <div style="font-size: 0.7rem; color: #666; margin-bottom: 4px;">Pending Payments</div>
                                    <div id="sales-pending" style="font-size: 1.5rem; font-weight: bold; color: #000;">—</div>
                                </div>
                                <div class="wi-border">
                                    <div style="font-size: 0.7rem; color: #666; margin-bottom: 4px;">New Customers</div>
                                    <div id="sales-customers" style="font-size: 1.5rem; font-weight: bold; color: #000;">—</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <h3>
                                <i class="ti ti-alert-triangle"></i>
                                <span class="wi-highlight">Alerts</span>
                            </h3>
                            <div class="status-card status-urgent">
                                <div>
                                    <strong>Low Stock Alert</strong>
                                    <div style="font-size: 0.7rem; color: #666;">Chemical XYZ below minimum</div>
                                </div>
                                <button class="btn btn-primary" style="padding: 3px 6px; font-size: 0.66rem;" onclick="showToaster('success', 'Reorder Initiated', 'Purchase order created')">Reorder</button>
                            </div>
                            <div class="status-card status-pending">
                                <div>
                                    <strong>QC Pending</strong>
                                    <div style="font-size: 0.7rem; color: #666;">Batch #1234 awaiting approval</div>
                                </div>
                                <button class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.66rem;" onclick="showToaster('info', 'Opening QC', 'Loading details...')">Review</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <div class="table-header">
                            <h3><i class="ti ti-receipt-2"></i> Recent Orders</h3>
                            <button class="btn btn-primary" onclick="showModule('orders')">View All</button>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-tbody">
                                <!-- populated by fetchRecentOrders() -->
                            </tbody>
                        </table>
                    </div>
                </div>

    <div id="toaster-container" class="toaster-container"></div>

    <script>
        async function fetchDashboard() {
            try {
                const res = await fetch('/api/dashboard/summary', { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Failed to fetch dashboard summary');
                const json = await res.json();

                const production = json.production || {};
                const inventory = json.inventory || {};
                const sales = json.sales || {};

                // Production
                document.getElementById('production-active').textContent = production.active_orders ?? '0';
                document.getElementById('production-active-desc').textContent = (production.active_orders ?? 0) + ' OF in progress';
                document.getElementById('production-pending').textContent = production.pending_launch ?? '0';
                document.getElementById('production-pending-desc').textContent = (production.pending_launch ?? 0) + ' orders scheduled';
                document.getElementById('production-completed').textContent = production.completed_today ?? '0';
                document.getElementById('production-completed-desc').textContent = (production.completed_today ?? 0) + ' orders finished';

                // Inventory
                const raw = inventory.raw_materials_pct ?? 0;
                const pack = inventory.packaging_pct ?? 0;
                const fin = inventory.finished_products_pct ?? 0;
                document.getElementById('inventory-raw-pct').textContent = raw + '%';
                document.getElementById('inventory-raw').style.width = raw + '%';
                document.getElementById('inventory-pack-pct').textContent = pack + '%';
                document.getElementById('inventory-pack').style.width = pack + '%';
                document.getElementById('inventory-fin-pct').textContent = fin + '%';
                document.getElementById('inventory-fin').style.width = fin + '%';

                // Sales
                document.getElementById('sales-monthly').textContent = sales.monthly_revenue ? ('DZD ' + Number(sales.monthly_revenue).toLocaleString()) : '—';
                document.getElementById('sales-quotes').textContent = sales.active_quotes ?? '0';
                document.getElementById('sales-pending').textContent = sales.pending_payments ? ('DZD ' + Number(sales.pending_payments).toLocaleString()) : '—';
                document.getElementById('sales-customers').textContent = sales.new_customers ?? '0';

            } catch (err) {
                console.error('Dashboard fetch error', err);
            }
        }

        // fetch recent orders and populate the Recent Orders table
        async function fetchRecentOrders() {
            try {
                const res = await fetch('/api/dashboard/recent-orders', { credentials: 'same-origin' });
                if (!res.ok) return;
                const json = await res.json();
                const data = json.data || [];
                const tbody = document.getElementById('recent-orders-tbody');
                if (!tbody) return;
                tbody.innerHTML = '';
                data.forEach(o => {
                    const tr = document.createElement('tr');
                    const orderNumber = o.order_number || ('ORD-' + o.id);
                    const customer = (o.customer && (o.customer.company_name || o.customer.name)) || '';
                    const product = (o.product && (o.product.name || o.product.product_code)) || '';
                    const quantity = (o.quantity ? (o.quantity + (o.unit ? (' ' + o.unit) : '')) : '');
                    const status = o.status || '';
                    const created = o.created_at ? new Date(o.created_at).toLocaleString() : '';

                    tr.innerHTML = `
                        <td><strong>${orderNumber}</strong><div style="font-size:0.75rem;color:#6b7280">${created}</div></td>
                        <td>${customer}</td>
                        <td>${product}</td>
                        <td>${quantity}</td>
                        <td><span class="badge badge-${status === 'completed' ? 'success' : status === 'pending' ? 'warning' : 'info'}">${status ? status.toUpperCase() : ''}</span></td>
                        <td><button class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.66rem;" onclick="viewOrder('${o.id}')">View</button></td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                console.error('Failed to fetch recent orders', err);
            }
        }

        // fetch recent invoices into a simple table (if present)
        async function fetchRecentInvoices() {
            try {
                const res = await fetch('/api/dashboard/recent-invoices', { credentials: 'same-origin' });
                if (!res.ok) return;
                const json = await res.json();
                const data = json.data || [];
                // if there's a table with tbody for recent invoices, populate first
                const tbody = document.querySelector('.table-container table tbody');
                if (!tbody) return;
                tbody.innerHTML = '';
                data.forEach(inv => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${inv.invoice_number ?? ('INV-' + inv.id)}</strong></td>
                        <td>${inv.customer ? inv.customer.company_name : ''}</td>
                        <td>DZD ${Number(inv.total ?? inv.amount ?? 0).toLocaleString()}</td>
                        <td>${inv.paid_amount ? ('DZD ' + Number(inv.paid_amount).toLocaleString()) : 'DZD 0.00'}</td>
                        <td style="color: ${inv.balance > 0 ? '#f57c00':'#10b981'}; font-weight:600;">${inv.balance ? ('DZD ' + Number(inv.balance).toLocaleString()) : 'DZD 0.00'}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="progress-bar" style="width:80px; height:8px;"><div class="progress-fill" style="width:${inv.percent_paid ?? 0}%; background:${inv.percent_paid >= 100 ? '#10b981':'#3b82f6'}"></div></div>
                                <span style="font-size:0.8rem;">${inv.percent_paid ?? 0}%</span>
                            </div>
                        </td>
                        <td>${inv.days_outstanding ?? ''}</td>
                        <td><span class="badge badge-${inv.status === 'paid' ? 'success' : inv.status === 'overdue' ? 'danger':'warning'}">${inv.status?.toUpperCase() ?? ''}</span></td>
                        <td><button class="btn btn-secondary" onclick="viewInvoice('${inv.id}')">Details</button></td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                console.error('Failed to fetch recent invoices', err);
            }
        }

        function viewInvoice(id){
            // simple navigation - open invoices module or invoice page
            window.location.href = '/invoicing';
        }

        function viewOrder(id){
            // navigate to order details or orders module
            // if a dedicated order view exists, prefer it, otherwise open orders list
            window.location.href = '/orders';
        }

        document.addEventListener('DOMContentLoaded', function(){
            fetchDashboard();
            fetchRecentInvoices();
            fetchRecentOrders();
        });
    </script>


    
    <script>
        // use global showToaster/removeToaster from layout; local duplicates removed to avoid overriding global helpers

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

        function toggleCustomerFields() {
            const type = document.getElementById('customerType').value;
            const businessFields = document.getElementById('businessFields');
            if (businessFields) {
                businessFields.style.display = type === 'business' ? 'block' : 'none';
            }
        }

        function toggleMaterialFields() {
            const type = document.getElementById('materialType').value;
            const fields = document.getElementById('materialFields');
            if (fields) {
                fields.style.display = type ? 'block' : 'none';
            }
        }

        function viewItem(id) {
            showModal();
            document.getElementById('modal-title').textContent = `View Details - ${id}`;
            document.getElementById('modal-body').innerHTML = `
                <div style="padding: 20px;">
                    <p><strong>ID:</strong> ${id}</p>
                    <p><strong>Status:</strong> Active</p>
                    <p style="margin-top: 15px; color: #666;">Additional details would be displayed here.</p>
                    <div style="margin-top: 20px;">
                        <button class="btn btn-secondary" onclick="hideModal()">Close</button>
                    </div>
                </div>
            `;
        }

        function viewOrderDetails(orderId) {
            showModal();
            document.getElementById('modal-title').textContent = `Order Details - ${orderId}`;
            
            const orders = {
                'ORD-2024-001': {
                    customer: 'Cosmetic Solutions Ltd.',
                    product: 'Face Cream Base',
                    quantity: '500 L',
                    value: 'DZD 18,750',
                    orderDate: '2024-03-01',
                    deliveryDate: '2024-03-15',
                    status: 'Completed',
                    invoice: 'INV-2024-0156',
                    invoiceStatus: 'Unpaid',
                    production: 'OF-2024-0156'
                },
                'ORD-2024-002': {
                    customer: 'PharmaTech Industries',
                    product: 'Supplement Capsules',
                    quantity: '10,000 units',
                    value: 'DZD 45,200',
                    orderDate: '2024-02-28',
                    deliveryDate: '2024-03-10',
                    status: 'Completed',
                    invoice: 'INV-2024-0155',
                    invoiceStatus: 'Paid',
                    production: 'OF-2024-0155'
                },
                'ORD-2024-003': {
                    customer: 'Beauty Global Corp',
                    product: 'Body Lotion',
                    quantity: '250 L',
                    value: 'DZD 12,890',
                    orderDate: '2024-02-15',
                    deliveryDate: '2024-03-01',
                    status: 'Completed',
                    invoice: 'INV-2024-0142',
                    invoiceStatus: 'Overdue',
                    production: 'OF-2024-0142'
                },
                'ORD-2024-004': {
                    customer: 'Wellness Labs Inc.',
                    product: 'Anti-Aging Cream',
                    quantity: '300 units',
                    value: 'DZD 28,450',
                    orderDate: '2024-02-10',
                    deliveryDate: '2024-02-25',
                    status: 'Completed',
                    invoice: 'INV-2024-0138',
                    invoiceStatus: 'Partially Paid',
                    production: 'OF-2024-0138'
                },
                'ORD-2024-005': {
                    customer: 'Natural Care Products',
                    product: 'Face Cream Base',
                    quantity: '200 L',
                    value: 'DZD 7,500',
                    orderDate: '2024-03-12',
                    deliveryDate: '2024-03-25',
                    status: 'In Production',
                    invoice: null,
                    invoiceStatus: 'Not Created',
                    production: 'OF-2024-0165'
                }
            };

            const order = orders[orderId] || orders['ORD-2024-001'];

            const invoiceSection = order.invoice ? `
                <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid ${order.invoiceStatus === 'Paid' ? '#10b981' : order.invoiceStatus === 'Overdue' ? '#ef4444' : '#f59e0b'};">
                    <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #2c2c2c;">Invoice Information</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <div style="font-size: 0.7rem; color: #666;">Invoice Number</div>
                            <div style="font-weight: 600;">${order.invoice}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: #666;">Payment Status</div>
                            <div style="font-weight: 600; color: ${order.invoiceStatus === 'Paid' ? '#10b981' : order.invoiceStatus === 'Overdue' ? '#ef4444' : '#f59e0b'};">${order.invoiceStatus}</div>
                        </div>
                    </div>
                    <button class="btn btn-primary" style="width: 100%; margin-top: 10px;" onclick="hideModal(); setTimeout(() => viewInvoiceTracking('${order.invoice}'), 300);">
                        <i class="ti ti-file-invoice"></i> View Invoice & Payment Tracking
                    </button>
                </div>
            ` : `
                <div style="margin-top: 20px; padding: 15px; background: #fff8e1; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #2c2c2c;">Invoice Not Created</h4>
                    <p style="font-size: 0.8rem; color: #666; margin-bottom: 10px;">This order has not been invoiced yet. Create an invoice to enable payment tracking.</p>
                    <button class="btn btn-primary" style="width: 100%;" onclick="hideModal(); setTimeout(() => createInvoiceFromOrder('${orderId}'), 300);">
                        <i class="ti ti-plus"></i> Create Invoice from Order
                    </button>
                </div>
            `;

            document.getElementById('modal-body').innerHTML = `
                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 15px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Customer</div>
                            <div style="font-weight: 600; font-size: 1.1rem;">${order.customer}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Order Status</div>
                            <div style="font-weight: 600; font-size: 1.1rem;">${order.status}</div>
                        </div>
                    </div>

                    <div style="padding: 15px; background: white; border-radius: 6px; margin-bottom: 15px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 0.9rem;">Order Items</h4>
                        <div style="display: flex; justify-content: space-between; padding: 8px; background: #f9f9f9; border-radius: 4px;">
                            <div>
                                <div style="font-weight: 600;">${order.product}</div>
                                <div style="font-size: 0.75rem; color: #666;">Quantity: ${order.quantity}</div>
                            </div>
                            <div style="font-weight: bold; font-size: 1.1rem;">${order.value}</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Order Date</div>
                            <div style="font-weight: 600;">${order.orderDate}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Delivery Date</div>
                            <div style="font-weight: 600;">${order.deliveryDate}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Production Order</div>
                            <div style="font-weight: 600;">${order.production}</div>
                        </div>
                    </div>
                </div>

                ${invoiceSection}

                <div style="display: flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;">
                    <button class="btn btn-secondary" onclick="hideModal()">Close</button>
                </div>
            `;
        }

        function createInvoiceFromOrder(orderId) {
            hideModal();
            setTimeout(() => {
                createItem('invoice');
                showToaster('info', 'Pre-filled', `Invoice form populated with order ${orderId} details`);
            }, 100);
        }

        function viewInvoiceTracking(invoiceId) {
            showModal();
            document.getElementById('modal-title').textContent = `Payment Tracking - ${invoiceId}`;
            
            const data = {
                'INV-2024-0156': {
                    orderId: 'ORD-2024-001',
                    customer: 'Cosmetic Solutions Ltd.',
                    total: 'DZD 18,750',
                    paid: 'DZD 0',
                    balance: 'DZD 18,750',
                    status: 'Unpaid',
                    statusColor: '#f57c00',
                    percent: 0,
                    issueDate: '2024-03-01',
                    dueDate: '2024-03-31',
                    daysOutstanding: '28 days',
                    payments: []
                },
                'INV-2024-0155': {
                    orderId: 'ORD-2024-002',
                    customer: 'PharmaTech Industries',
                    total: 'DZD 45,200',
                    paid: 'DZD 45,200',
                    balance: 'DZD 0',
                    status: 'Paid',
                    statusColor: '#10b981',
                    percent: 100,
                    issueDate: '2024-02-28',
                    dueDate: '2024-03-29',
                    daysOutstanding: 'Paid',
                    payments: [
                        {date: '2024-03-08', amount: 'DZD 45,200', method: 'Bank Transfer', ref: 'TXN-2024-0308-001'}
                    ]
                },
                'INV-2024-0142': {
                    orderId: 'ORD-2024-003',
                    customer: 'Beauty Global Corp',
                    total: 'DZD 12,890',
                    paid: 'DZD 0',
                    balance: 'DZD 12,890',
                    status: 'Overdue',
                    statusColor: '#ef4444',
                    percent: 0,
                    issueDate: '2024-02-15',
                    dueDate: '2024-03-15',
                    daysOutstanding: '14 days overdue',
                    payments: []
                },
                'INV-2024-0138': {
                    orderId: 'ORD-2024-004',
                    customer: 'Wellness Labs Inc.',
                    total: 'DZD 28,450',
                    paid: 'DZD 20,000',
                    balance: 'DZD 8,450',
                    status: 'Partially Paid',
                    statusColor: '#3b82f6',
                    percent: 70,
                    issueDate: '2024-02-10',
                    dueDate: '2024-03-10',
                    daysOutstanding: '18 days',
                    payments: [
                        {date: '2024-03-10', amount: 'DZD 20,000', method: 'Bank Transfer', ref: 'TXN-2024-0310-001'}
                    ]
                }
            };
            const inv = data[invoiceId] || data['INV-2024-0156'];

            let paymentsHtml = '';
            if (inv.payments.length > 0) {
                paymentsHtml = `
                    <h4 style="color: rgb(20, 54, 25); margin: 20px 0 15px 0; font-size: 0.95rem;">Payment History</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9f9f9;">
                                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 0.75rem;">Date</th>
                                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 0.75rem;">Amount</th>
                                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 0.75rem;">Method</th>
                                <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 0.75rem;">Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${inv.payments.map(p => `
                                <tr>
                                    <td style="padding: 8px; border-bottom: 1px solid #f0f0f0; font-size: 0.8rem;">${p.date}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 0.8rem;">${p.amount}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f0f0f0; font-size: 0.8rem;">${p.method}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #f0f0f0; font-size: 0.8rem;">${p.ref}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } else {
                paymentsHtml = `
                    <div style="background: #fff8e1; padding: 15px; border-radius: 6px; margin-top: 20px; border-left: 3px solid #f57c00;">
                        <strong>No Payments Recorded</strong>
                        <p style="margin: 5px 0 0 0; font-size: 0.8rem; color: #666;">No payments have been recorded for this invoice yet.</p>
                    </div>
                `;
            }

            document.getElementById('modal-body').innerHTML = `
                <div style="background: #e3f2fd; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px; border-left: 3px solid #3b82f6;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Related Order</div>
                            <div style="font-weight: 600; font-size: 1rem;">${inv.orderId}</div>
                        </div>
                        <button class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.75rem;" onclick="hideModal(); setTimeout(() => viewOrderDetails('${inv.orderId}'), 300);">
                            <i class="ti ti-arrow-back"></i> View Order
                        </button>
                    </div>
                </div>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid ${inv.statusColor};">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Customer</div>
                            <div style="font-weight: 600; font-size: 1.1rem;">${inv.customer}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Issue Date</div>
                            <div style="font-weight: 600; font-size: 1.1rem;">${inv.issueDate}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Due Date</div>
                            <div style="font-weight: 600; font-size: 1.1rem;">${inv.dueDate}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 5px;">Days Outstanding</div>
                            <div style="font-weight: 600; font-size: 1.1rem; color: ${inv.statusColor};">${inv.daysOutstanding}</div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; padding-top: 15px; border-top: 1px solid #e5e5e5;">
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Invoice Total</div>
                            <div style="font-size: 1.3rem; font-weight: bold; color: #000;">${inv.total}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Amount Paid</div>
                            <div style="font-size: 1.3rem; font-weight: bold; color: #10b981;">${inv.paid}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #666;">Balance Due</div>
                            <div style="font-size: 1.3rem; font-weight: bold; color: ${inv.statusColor};">${inv.balance}</div>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 0.8rem; font-weight: 600;">Payment Progress</span>
                            <span style="font-size: 0.8rem; font-weight: 600;">${inv.percent}%</span>
                        </div>
                        <div class="progress-bar" style="height: 10px;">
                            <div class="progress-fill" style="width: ${inv.percent}%; background: ${inv.statusColor};"></div>
                        </div>
                    </div>

                    <div style="margin-top: 15px; text-align: center;">
                        <span style="padding: 6px 16px; background: white; border-radius: 20px; font-weight: 600; color: ${inv.statusColor}; font-size: 0.85rem;">${inv.status}</span>
                    </div>
                </div>

                ${paymentsHtml}

                <div style="display: flex; gap: 12px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5;">
                    <button class="btn btn-primary" onclick="recordPaymentFor('${invoiceId}')">
                        <i class="ti ti-cash"></i> Record Payment
                    </button>
                    ${inv.balance !== 'DZD 0' ? `
                        <button class="btn btn-secondary" onclick="sendPaymentReminder('${invoiceId}')">
                            <i class="ti ti-mail"></i> Send Reminder
                        </button>
                    ` : ''}
                    <button class="btn btn-secondary" onclick="hideModal()">Close</button>
                </div>
            `;
        }

        function recordPaymentFor(invoiceId) {
            hideModal();
            setTimeout(() => {
                createItem('payment');
            }, 300);
        }

        function sendPaymentReminder(invoiceId) {
            showToaster('success', 'Reminder Sent', `Payment reminder for ${invoiceId} has been sent to the customer`);
            hideModal();
        }

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

        function formatTitle(type) {
            return type.split('-').map(word => 
                word.charAt(0).toUpperCase() + word.slice(1)
            ).join(' ');
        }

        function submitForm(type) {
            hideModal();
            showToaster('success', 'Success!', `${formatTitle(type)} created successfully!`);
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
        });
    </script>

</div>
@endsection

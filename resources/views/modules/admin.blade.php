@extends('layouts.app')

@section('title', 'Administration')

@section('content')
    <div class="content">
        <div id="admin" class="module active">
            <div class="tabs">
                <div class="tab-nav">
                    <button class="tab-button active" onclick="showTab('admin', 'profile', this)">My Profile</button>
                    {{-- <button class="tab-button" onclick="showTab('admin', 'users', this)">Users</button> --}}
                    <button class="tab-button" onclick="showTab('admin', 'settings', this)">Settings</button>
                </div>
            </div>

            <div id="admin-profile" class="tab-content active">
                <div class="card">
                    <form id="admin-profile-form" action="/admin/profile" method="post">
                        @csrf
                        @method('PUT')
                    <h3 style="margin-bottom: 20px;"><i class="ti ti-user-circle"></i> Administrator Profile</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-input" value="{{ Auth::user()->first_name }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-input" value="{{ Auth::user()->last_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="{{ Auth::user()->email }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" value="{{ Auth::user()->phone }}">
                    </div>
                    {{-- <div class="form-group">
                        @if(Auth::user()->isAdmin())
                        <label class="form-label">Department</label>
                        <input type="text" class="form-input" value="{{ Auth::user()->department}}"
                            style="background: #f5f5f5;">
                        @else  
                        <label class="form-label">Department</label>
                        <input type="text" class="form-input" value="{{ Auth::user()->department}}" readonly
                            style="background: #f5f5f5;">  
                        @endif    
                    </div> --}}
                    <button id="admin-profile-save" class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy"></i> Save Changes
                    </button>
                    </form>
                </div>
            </div>

            {{-- <div id="admin-users" class="tab-content">
                <div class="module-header">
                    <button class="btn btn-primary" onclick="createItem('user')"><i class="ti ti-user-plus"></i> Add
                        User</button>
                    <input type="search" class="search-input" placeholder="Search users...">
                </div>
                <div class="table-container">
                    <div class="table-header">
                        <h3>User Management</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>USR-001</td>
                                <td>Pierre Blanc</td>
                                <td>pierre.blanc@whiteindustry.com</td>
                                <td><span class="badge badge-info">Administrator</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>USR-002</td>
                                <td>Marie Dubois</td>
                                <td>marie.dubois@whiteindustry.com</td>
                                <td><span class="badge badge-info">Manager</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> --}}

            <div id="admin-settings" class="tab-content">
                <div class="card">
                    <form id="admin-settings-form" action="/admin/settings" method="post">
                        @csrf
                        @method('PUT')
                        <h3 style="margin-bottom: 20px;"><i class="ti ti-settings"></i> System Settings</h3>
                        <div class="form-group">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-input" value="White Industry">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Tax ID</label>
                                <input type="text" name="tax_id" class="form-input" value="DZ-123456789">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-input" value="+213 21 123 456">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Default Currency</label>
                            <select class="form-select" name="default_currency">
                                <option value="DZD" selected>DZD - Algerian Dinar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="USD">USD - US Dollar</option>
                            </select>
                        </div>
                        <button id="admin-settings-save" type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

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
                    payments: [{
                        date: '2024-03-08',
                        amount: 'DZD 45,200',
                        method: 'Bank Transfer',
                        ref: 'TXN-2024-0308-001'
                    }]
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
                    payments: [{
                        date: '2024-03-10',
                        amount: 'DZD 20,000',
                        method: 'Bank Transfer',
                        ref: 'TXN-2024-0310-001'
                    }]
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

    // AJAX submit for profile form (exposed on window so inline onclick can call it)
    window.submitProfile = async function (e) {
            if (e && e.preventDefault) e.preventDefault();
            const form = document.getElementById('admin-profile-form');
            if (!form) return;
            const url = form.getAttribute('action') || window.location.pathname;
            const fd = new FormData(form);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Accept JSON or plain text. Show first useful message as a toast.
                const ctype = res.headers.get('content-type') || '';
                if (res.status === 422 && ctype.includes('application/json')) {
                    const json = await res.json();
                    const errors = json.errors || {};
                    const first = Object.values(errors)[0];
                    showToaster('error', 'Validation failed', Array.isArray(first) ? first.join(', ') : first);
                    return;
                }

                if (ctype.includes('application/json')) {
                    const data = await res.json();
                    const msg = data.message || (data.success ? 'Saved successfully' : JSON.stringify(data));
                    showToaster(data.success ? 'success' : 'error', data.success ? 'Profile updated' : 'Error', msg);
                    return;
                }

                // Fallback: text response (or HTML). Show it in a toast (trimmed).
                const text = await res.text();
                const trimmed = text.trim().slice(0, 1000);
                showToaster('info', 'Response', trimmed || 'Saved');
            } catch (err) {
                console.error(err);
                showToaster('error', 'Network error', 'Could not contact server');
            }
        }

        // AJAX submit for settings form (exposed on window so inline onclick can call it)
        window.submitSettings = async function (e) {
            if (e && e.preventDefault) e.preventDefault();
            const form = document.getElementById('admin-settings-form');
            if (!form) return;
            const url = form.getAttribute('action') || window.location.pathname;
            const fd = new FormData(form);
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const ctype = res.headers.get('content-type') || '';
                if (res.status === 422 && ctype.includes('application/json')) {
                    const json = await res.json();
                    const errors = json.errors || {};
                    const first = Object.values(errors)[0];
                    showToaster('error', 'Validation failed', Array.isArray(first) ? first.join(', ') : first);
                    return;
                }

                if (ctype.includes('application/json')) {
                    const data = await res.json();
                    const msg = data.message || (data.success ? 'Saved successfully' : JSON.stringify(data));
                    showToaster(data.success ? 'success' : 'error', data.success ? 'Settings saved' : 'Error', msg);
                    return;
                }

                const text = await res.text();
                const trimmed = text.trim().slice(0, 1000);
                showToaster('info', 'Response', trimmed || 'Saved');
            } catch (err) {
                console.error(err);
                showToaster('error', 'Network error', 'Could not contact server');
            }
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

            // Bind save buttons to AJAX handlers so clicking "Save" triggers the requests
            const profileSaveBtn = document.getElementById('admin-profile-save');
            if (profileSaveBtn) {
                profileSaveBtn.addEventListener('click', function (e) {
                    if (typeof window.submitProfile === 'function') window.submitProfile(e);
                });
            }

            const settingsSaveBtn = document.getElementById('admin-settings-save');
            if (settingsSaveBtn) {
                settingsSaveBtn.addEventListener('click', function (e) {
                    if (typeof window.submitSettings === 'function') window.submitSettings(e);
                });
            }

            // Also bind the forms' submit event as a fallback (pressing Enter)
            const profileForm = document.getElementById('admin-profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', function (e) {
                    if (typeof window.submitProfile === 'function') window.submitProfile(e);
                });
            }

            const settingsForm = document.getElementById('admin-settings-form');
            if (settingsForm) {
                settingsForm.addEventListener('submit', function (e) {
                    if (typeof window.submitSettings === 'function') window.submitSettings(e);
                });
            }
        });
    </script>
@endsection

<!-- resources/views/invoices/create.blade.php -->
@extends('layouts.app')
@section('title', 'Create Invoice')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>Create Invoice</h2>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
                @csrf

                <!-- PRODUCTION SELECTION -->
                <div class="form-section">
                    <label>Select Completed Production</label>
                    <select name="production_id" id="production_select" class="form-control" required>
                        <option value="">-- Select Production --</option>
                        @foreach($productions as $production)
                            <option value="{{ $production->id }}">
                                {{ $production->production_number }} - {{ $production->order->order_number }}
                            </option>
                        @endforeach
                    </select>
                    <div id="production-loading" class="mt-2" style="display: none;">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="ml-2">Loading production details...</span>
                    </div>
                </div>

                <!-- PRODUCTION DETAILS -->
                <div id="production-details" style="display: none;">
                    <div class="form-section">
                        <h4>Production Summary</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Order:</strong> <span id="order-number"></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Customer:</strong> <span id="customer-name"></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Order Total:</strong> <span id="order-total"></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Production Date:</strong> <span id="production-date"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- INVOICE DETAILS -->
                    <div class="form-section">
                        <h4>Invoice Details</h4>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tax Percentage (%)</label>
                            <input type="number" name="tax_percentage" class="form-control" step="0.01" value="0" required>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- INVOICE ITEMS -->
                    <div class="form-section">
                        <h4>Invoice Items</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="invoiceItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Produced Quantity</th>
                                        <th>Invoice Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-items-tbody">
                                    <!-- Items loaded dynamically via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TOTALS -->
                    <div class="form-section" style="max-width: 500px; margin-left: auto;">
                        <div class="row">
                            <div class="col-6"><strong>Subtotal:</strong></div>
                            <div class="col-6 text-right"><span id="subtotal">$0.00</span></div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Tax:</strong></div>
                            <div class="col-6 text-right"><span id="tax-amount">$0.00</span></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6"><strong>Total:</strong></div>
                            <div class="col-6 text-right"><strong><span id="total-amount">$0.00</span></strong></div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-success">Create Invoice</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productionSelect = document.getElementById('production_select');
    const productionDetails = document.getElementById('production-details');
    const loadingIndicator = document.getElementById('production-loading');

    // Set default due date to 30 days from now
    const dueDateInput = document.querySelector('input[name="due_date"]');
    const today = new Date();
    const dueDate = new Date(today);
    dueDate.setDate(today.getDate() + 30);
    dueDateInput.value = dueDate.toISOString().split('T')[0];

    productionSelect.addEventListener('change', function() {
        const productionId = this.value;
        
        if (!productionId) {
            productionDetails.style.display = 'none';
            return;
        }

        // Show loading indicator
        loadingIndicator.style.display = 'block';
        productionDetails.style.display = 'none';

        // Load production details via AJAX
        fetchProductionDetails(productionId);
    });

    // Add event listener for tax percentage change
    document.querySelector('input[name="tax_percentage"]').addEventListener('input', calculateTotals);
});

function fetchProductionDetails(productionId) {
    fetch(`/productions/${productionId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            displayProductionDetails(data);
        })
        .catch(error => {
            console.error('Error fetching production details:', error);
            alert('Error loading production details. Please try again.');
        })
        .finally(() => {
            document.getElementById('production-loading').style.display = 'none';
        });
}

function displayProductionDetails(production) {
    // Update production summary
    document.getElementById('order-number').textContent = production.order.order_number;
    document.getElementById('customer-name').textContent = production.order.customer.company_name;
    document.getElementById('order-total').textContent = '$' + parseFloat(production.order.total_amount).toFixed(2);
    document.getElementById('production-date').textContent = new Date(production.production_date).toLocaleDateString();

    // Load production items
    const tbody = document.getElementById('invoice-items-tbody');
    tbody.innerHTML = '';

    production.items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.order_product.product_name}</td>
            <td>${item.quantity_produced}</td>
            <td>
                <input type="hidden" name="invoice_items[${index}][production_item_id]" value="${item.id}">
                <input type="number" 
                       class="form-control invoice-quantity" 
                       name="invoice_items[${index}][quantity]" 
                       value="${item.quantity_produced}" 
                       min="0" 
                       max="${item.quantity_produced}"
                       onchange="calculateTotals()">
            </td>
            <td>
                <input type="number" 
                       class="form-control unit-price" 
                       name="invoice_items[${index}][unit_price]" 
                       value="${item.order_product.price_unit}" 
                       step="0.01"
                       onchange="calculateTotals()">
            </td>
            <td class="item-amount">DA 0.00</td>
        `;
        tbody.appendChild(row);
    });

    // Show production details and calculate initial totals
    document.getElementById('production-details').style.display = 'block';
    calculateTotals();
}

function calculateTotals() {
    const rows = document.querySelectorAll('#invoice-items-tbody tr');
    let subtotal = 0;

    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.invoice-quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const amount = quantity * unitPrice;

        row.querySelector('.item-amount').textContent = '$' + amount.toFixed(2);
        subtotal += amount;
    });

    const taxPercentage = parseFloat(document.querySelector('input[name="tax_percentage"]').value) || 0;
    const taxAmount = (subtotal * taxPercentage) / 100;
    const total = subtotal + taxAmount;

    document.getElementById('subtotal').textContent = 'DA' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = 'DA' + taxAmount.toFixed(2);
    document.getElementById('total-amount').textContent = 'DA' + total.toFixed(2);
}
</script>

@endsection
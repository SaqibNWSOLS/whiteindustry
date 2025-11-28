<!-- resources/views/invoices/edit.blade.php -->
@extends('layouts.app')
@section('title', 'Edit Invoice')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>Edit Invoice {{ $invoice->invoice_number }}</h2>
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

            <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoiceForm">
                @csrf
                @method('PUT')

                <!-- PRODUCTION & ORDER INFO -->
                <div class="form-section">
                    <h4>Production & Order Information</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Production:</strong> {{ $invoice->production->production_number }}</p>
                        </div>
                   
                        <div class="col-md-3">
                            <p><strong>Customer:</strong> {{ $invoice->customer->company_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Current Status:</strong> <span class="badge badge-warning">{{ ucfirst($invoice->status) }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- INVOICE DETAILS -->
                <div class="form-section">
                    <h4>Invoice Details</h4>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" class="form-control" required value="{{ $invoice->invoice_date }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Due Date</label>
                            <input type="date" name="due_date" class="form-control" required value="{{ $invoice->due_date }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tax Percentage (%)</label>
                        <input type="number" name="tax_percentage" class="form-control" step="0.01" value="{{ $invoice->getTaxPercentage() }}" required onchange="calculateTotals()">
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $invoice->notes }}</textarea>
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
                                @foreach($invoice->production->items as $index => $item)
                                    @php
                                        $invoiceItem = $invoice->items->firstWhere('production_item_id', $item->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $item->orderProduct->product_name }}</td>
                                        <td>{{ $item->quantity_produced }}</td>
                                        <td>
                                            <input type="hidden" name="invoice_items[{{ $index }}][production_item_id]" value="{{ $item->id }}">
                                            <input type="number" 
                                                   class="form-control invoice-quantity" 
                                                   name="invoice_items[{{ $index }}][quantity]" 
                                                   value="{{ $invoiceItem->quantity ?? $item->quantity_produced }}" 
                                                   min="0" 
                                                   max="{{ $item->quantity_produced }}"
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control unit-price" 
                                                   name="invoice_items[{{ $index }}][unit_price]" 
                                                   value="{{ $invoiceItem->unit_price ?? 0 }}" 
                                                   step="0.01"
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td class="item-amount">${{ number_format($invoiceItem->amount ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TOTALS -->
                <div class="form-section" style="max-width: 500px; margin-left: auto;">
                    <div class="row">
                        <div class="col-6"><strong>Subtotal:</strong></div>
                        <div class="col-6 text-right"><span id="subtotal">${{ number_format($invoice->subtotal, 2) }}</span></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Tax:</strong></div>
                        <div class="col-6 text-right"><span id="tax-amount">${{ number_format($invoice->tax_amount, 2) }}</span></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-right"><strong><span id="total-amount">${{ number_format($invoice->total_amount, 2) }}</span></strong></div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('total-amount').textContent = '$' + total.toFixed(2);
}

// Calculate totals on page load
window.addEventListener('load', calculateTotals);
</script>

@endsection
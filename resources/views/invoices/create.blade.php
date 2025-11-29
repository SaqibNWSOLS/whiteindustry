@extends('layouts.app')
@section('title', __('invoice.create.title'))

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>{{ __('invoice.create.title') }}</h2>
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

            <form action="{{ route('invoices.create') }}" method="GET" id="invoiceForm">
                <!-- PRODUCTION SELECTION -->
                <div class="form-section">
                    <label>{{ __('invoice.create.select_production') }}</label>
                    <select name="production_id" id="production_select" class="form-control" required onchange="this.form.submit()">
                        <option value="">{{ __('invoice.create.select_production_placeholder') }}</option>
                        @foreach($productions as $production)
                            <option value="{{ $production->id }}" 
                                {{ $selectedProduction && $selectedProduction->id == $production->id ? 'selected' : '' }}>
                                {{ $production->production_number }} - {{ $production->order->order_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if($selectedProduction)
                <!-- PRODUCTION DETAILS -->
                <div id="production-details">
                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="production_id" value="{{ $selectedProduction->id }}">

                        <div class="form-section">
                            <h4>{{ __('invoice.create.production_summary') }}</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>{{ __('invoice.create.order') }}:</strong> <span id="order-number">{{ $selectedProduction->order->order_number }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>{{ __('invoice.create.customer') }}:</strong> <span id="customer-name">{{ $selectedProduction->order->customer->company_name }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>{{ __('invoice.create.order_total') }}:</strong> <span id="order-total">DA {{ number_format($selectedProduction->order->total_amount, 2) }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>{{ __('invoice.create.production_date') }}:</strong> <span id="production-date">{{ \Carbon\Carbon::parse($selectedProduction->production_date)->format('M d, Y') }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- INVOICE DETAILS -->
                        <div class="form-section">
                            <h4>{{ __('invoice.create.invoice_details') }}</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('invoice.create.invoice_date') }}</label>
                                    <input type="date" name="invoice_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('invoice.create.due_date') }}</label>
                                    <input type="date" name="due_date" class="form-control" required value="{{ now()->addDays(30)->format('Y-m-d') }}">
                                </div>
                            </div>
                            
                            <input type="hidden" name="tax_percentage" class="form-control" step="0.01" value="19" required oninput="calculateTotals()">
                            
                            <div class="form-group">
                                <label>{{ __('invoice.create.notes') }}</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- INVOICE ITEMS -->
                        <div class="form-section">
                            <h4>{{ __('invoice.create.invoice_items') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="invoiceItemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('invoice.create.product_name') }}</th>
                                            <th>{{ __('invoice.create.produced_quantity') }}</th>
                                            <th>{{ __('invoice.create.invoice_quantity') }}</th>
                                            <th>{{ __('invoice.create.unit_price') }}</th>
                                            <th>{{ __('invoice.create.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice-items-tbody">
                                        @foreach($selectedProduction->items as $index => $item)
                                        <tr>
                                            <td>{{ $item->orderProduct->product_name }}</td>
                                            <td>{{ $item->quantity_produced }}</td>
                                            <td>
                                                <input type="hidden" name="invoice_items[{{ $index }}][production_item_id]" value="{{ $item->id }}">
                                                <input type="number" 
                                                       class="form-control invoice-quantity" 
                                                       name="invoice_items[{{ $index }}][quantity]" 
                                                       value="{{ $item->quantity_produced }}" 
                                                       min="0" 
                                                       max="{{ $item->quantity_produced }}"
                                                       oninput="calculateTotals()">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control unit-price" 
                                                       name="invoice_items[{{ $index }}][unit_price]" 
                                                       value="{{ $item->orderProduct->price_unit }}" 
                                                       step="0.01"
                                                       oninput="calculateTotals()">
                                            </td>
                                            <td class="item-amount">DA 0.00</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TOTALS -->
                        <div class="form-section" style="max-width: 500px; margin-left: auto;">
                            <div class="row">
                                <div class="col-6"><strong>{{ __('invoice.create.subtotal') }}:</strong></div>
                                <div class="col-6 text-right"><span id="subtotal">DA 0.00</span></div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>{{ __('invoice.create.tax') }}:</strong></div>
                                <div class="col-6 text-right"><span id="tax-amount">DA 0.00</span></div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6"><strong>{{ __('invoice.create.total') }}:</strong></div>
                                <div class="col-6 text-right"><strong><span id="total-amount">DA 0.00</span></strong></div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" class="btn btn-success">{{ __('invoice.buttons.create_invoice') }}</button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">{{ __('invoice.buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

@if($selectedProduction)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate totals on page load
    calculateTotals();
});

function calculateTotals() {
    const rows = document.querySelectorAll('#invoice-items-tbody tr');
    let subtotal = 0;

    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.invoice-quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const amount = quantity * unitPrice;

        row.querySelector('.item-amount').textContent = 'DA ' + amount.toFixed(2);
        subtotal += amount;
    });

    const taxPercentage = parseFloat(document.querySelector('input[name="tax_percentage"]').value) || 0;
    const taxAmount = (subtotal * taxPercentage) / 100;
    const total = subtotal + taxAmount;

    document.getElementById('subtotal').textContent = 'DA ' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = 'DA ' + taxAmount.toFixed(2);
    document.getElementById('total-amount').textContent = 'DA ' + total.toFixed(2);
}
</script>
@endif

@endsection
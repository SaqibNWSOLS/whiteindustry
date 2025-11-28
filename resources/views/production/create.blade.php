@extends('layouts.app')
@section('title', 'Create Production')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>Create Production</h2>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('production.store') }}" method="POST">
                @csrf

                <!-- ORDER SELECTION -->
                <div class="form-section">
                    <label>Select Confirmed Order</label>
                    <select name="order_id" class="form-control" required onchange="loadOrderDetails(this.value)">
                        <option value="">-- Select Order --</option>
                        @foreach($confirmedOrders as $order)
                            <option value="{{ $order->id }}" 
                                data-order-num="{{ $order->order_number }}"
                                data-customer="{{ $order->customer->company_name }}"
                                data-total="{{ $order->total_amount }}"
                                data-order="{{ json_encode($order->products->map(function($p) { return ['id' => $p->id, 'name' => $p->product_name, 'quantity' => $p->quantity, 'type' => $p->product_type]; })) }}">
                                {{ $order->order_number }} - {{ $order->customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ORDER DETAILS SECTION -->
                <div id="order-details" style="display: none;">

                    <div class="form-section">
                        <h4 class="section-title">Order Summary</h4>
                        <div class="summary-box">
                            <p><strong>Order Number:</strong> <span id="order-num"></span></p>
                            <p><strong>Customer:</strong> <span id="customer-name"></span></p>
                            <p><strong>Total Amount:</strong> <span id="order-total"></span></p>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="section-title">Production Details</h4>

                        <div class="form-group">
                            <label>Production Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div class="form-group">
                            <label>Production End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Production Notes</label>
                            <textarea name="production_notes" class="form-control" rows="4" placeholder="Add any production notes..."></textarea>
                        </div>
                    </div>

                    <!-- PRODUCTION ITEMS SECTION -->
                    <div class="form-section">
                        <h4 class="section-title">Production Items</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Product Type</th>
                                        <th>Order Quantity</th>
                                        <th>Quantity to Produce</th>
                                    </tr>
                                </thead>
                                <tbody id="production-items-tbody">
                                    <!-- Items will be dynamically loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">Create Production</button>
                        <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadOrderDetails(orderId) {
    if (!orderId) {
        document.getElementById('order-details').style.display = 'none';
        return;
    }

    const selected = document.querySelector(`option[value="${orderId}"]`);
    document.getElementById('order-num').textContent = selected.dataset.orderNum;
    document.getElementById('customer-name').textContent = selected.dataset.customer;
    document.getElementById('order-total').textContent = '$' + parseFloat(selected.dataset.total).toFixed(2);

    // Load production items
    const products = JSON.parse(selected.dataset.order);
    const tbody = document.getElementById('production-items-tbody');
    tbody.innerHTML = '';

    products.forEach((product, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${product.name}</td>
            <td>${product.type}</td>
            <td>${product.quantity}</td>
            <td>
                <input type="hidden" name="production_items[${index}][order_product_id]" value="${product.id}">
                <input type="number" 
                       name="production_items[${index}][quantity_planned]" 
                       class="form-control" 
            readonly
                       value="${product.quantity}" 
                       min="1" 
                       max="${product.quantity * 2}"
                       required>
            </td>
        `;
        tbody.appendChild(row);
    });

    document.getElementById('order-details').style.display = 'block';
}
</script>

@endsection
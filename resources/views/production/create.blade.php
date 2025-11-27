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
                                data-customer="{{ $order->quote->customer->company_name }}"
                                data-total="{{ $order->total_amount }}">
                                {{ $order->order_number }} - {{ $order->quote->customer->company_name }}
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

    document.getElementById('order-details').style.display = 'block';
}
</script>

@endsection

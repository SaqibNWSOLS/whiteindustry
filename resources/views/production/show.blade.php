@extends('layouts.app')
@section('title', 'Production Details')

@section('content')
<style>
    .card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        padding: 20px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: transparent !important;
        border-bottom: 1px solid #eef1f7;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .card-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #333;
    }

    .badge {
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 8px;
        font-weight: 600;
    }
    .badge-success { background: #d4f8e8; color: #1a8b4c; }
    .badge-warning { background: #fff4d4; color: #b88600; }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #444;
        margin-bottom: 10px;
    }

    .info-box {
        background: #f9fbff;
        padding: 15px;
        border: 1px solid #e0e8ff;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .info-box p {
        margin: 6px 0;
        font-size: 15px;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    table thead tr th {
        background: #f5f7fb;
        padding: 12px;
        text-align: left;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
    }

    table tbody tr {
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 6px;
    }

    table tbody tr td {
        padding: 12px;
        font-size: 14px;
        border-top: 1px solid #f0f0f0;
    }

    .invoice-card {
        padding: 14px;
        border: 1px solid #eaeaea;
        border-radius: 10px;
        background: #fafafa;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

</style>

<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>{{ $production->production_number }}</h2>
            <span class="badge {{ $production->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                {{ ucfirst(str_replace('_', ' ', $production->status)) }}
            </span>
        </div>

        <div class="card-body">
            <div class="form-section">
                <h4>Production Details</h4>
                <p><strong>Order:</strong> {{ $production->order->order_number }}</p>
                <p><strong>Customer:</strong> {{ $production->order->quote->customer->company_name }}</p>
                <p><strong>Start Date:</strong> {{ $production->start_date??$production->start_date->format('Y-m-d') }}</p>
                <p><strong>End Date:</strong> {{ !empty($production->end_date) ? $production->end_date : 'In Progress' }}</p>
            </div>

            <div class="form-section">
                <h4>Order Items</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($production->order->products as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-section">
                <h4>Status Actions</h4>
                @if($production->status === 'pending')
                    <form action="{{ route('production.start', $production->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Start production?')">
                            Start Production
                        </button>
                    </form>
                @elseif($production->status === 'in_progress')
                    <form action="{{ route('production.complete', $production->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Complete production?')">
                            Complete Production
                        </button>
                    </form>
                @endif
            </div>

            <div class="form-section">
                <h4>Invoices ({{ $production->invoices->count() }})</h4>
                @forelse($production->invoices as $invoice)
                    <div style="padding: 10px; border: 1px solid #ddd; margin: 10px 0; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $invoice->invoice_number }}</strong>
                                <br>
                                <small>${{ number_format($invoice->total_amount, 2) }} · {{ ucfirst($invoice->status) }}</small>
                            </div>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-secondary">View</a>
                        </div>
                    </div>
                @empty
                    @if($production->status === 'completed')
                        <a href="{{ route('invoices.create', $production->id) }}" class="btn btn-primary">
                            Create Invoice
                        </a>
                    @else
                        <p>No invoices yet. Complete production to create invoices.</p>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection


@extends('layouts.app')
@section('title', 'Create Invoice')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>Create Invoice from Production</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('invoices.store') }}" method="POST">
                @csrf
                <input type="hidden" name="production_id" value="{{ $production->id }}">

                <div class="form-section">
                    <h4>Production Details</h4>
                    <p><strong>Production #:</strong> {{ $production->production_number }}</p>
                    <p><strong>Order #:</strong> {{ $production->order->order_number }}</p>
                    <p><strong>Customer:</strong> {{ $production->order->quote->customer->company_name }}</p>
                </div>

                <div class="form-section">
                    <h4>Order Items Summary</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd;">
                                <th style="text-align: left; padding: 10px;">Product</th>
                                <th style="text-align: center; padding: 10px;">Quantity</th>
                                <th style="text-align: right; padding: 10px;">Unit Price</th>
                                <th style="text-align: right; padding: 10px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subtotal = 0;
                            @endphp
                            @foreach($production->order->products as $item)
                                @php
                                    $subtotal += $item->total_price;
                                @endphp
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px;">{{ $item->product_name }}</td>
                                    <td style="text-align: center; padding: 10px;">{{ $item->quantity }}</td>
                                    <td style="text-align: right; padding: 10px;">${{ number_format($item->unit_price, 2) }}</td>
                                    <td style="text-align: right; padding: 10px;">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr style="border-top: 2px solid #ddd; font-weight: bold;">
                                <td colspan="3" style="text-align: right; padding: 10px;">Subtotal:</td>
                                <td style="text-align: right; padding: 10px;">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr style="font-weight: bold;">
                                <td colspan="3" style="text-align: right; padding: 10px;">Tax (19%):</td>
                                <td style="text-align: right; padding: 10px;">${{ number_format($subtotal * 0.19, 2) }}</td>
                            </tr>
                            <tr style="border-top: 2px solid #ddd; font-weight: bold; font-size: 1.1em;">
                                <td colspan="3" style="text-align: right; padding: 10px;">Total:</td>
                                <td style="text-align: right; padding: 10px;">${{ number_format($subtotal * 1.19, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-section">
                    <label>Invoice Date</label>
                    <input type="date" name="invoice_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="form-section">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control" required value="{{ now()->addDays(30)->format('Y-m-d') }}">
                </div>

                <div class="form-section">
                    <label>Invoice Notes (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Add invoice terms, payment instructions, etc..."></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success">Create Invoice</button>
                    <a href="{{ route('production.show', $production->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

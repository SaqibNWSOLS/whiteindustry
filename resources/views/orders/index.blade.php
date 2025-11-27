<!-- ==================== ORDERS INDEX VIEW ====================-->
<!-- resources/views/orders/index.blade.php -->

@extends('layouts.app')
@section('title', 'Orders')
@section('page_title', 'Orders Management')

@section('content')
<div class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-shopping-cart" style="color: #1976d2;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Total Orders</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">All orders</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-clock" style="color: #f57c00;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Pending</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['pending'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Awaiting confirmation</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e1f5fe;">
                <i class="fas fa-check-circle" style="color: #0288d1;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Confirmed</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['confirmed'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Confirmed orders</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #fff8e1;">
                <i class="fas fa-industry" style="color: #ffa000;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">In Production</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['production'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Currently in production</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-flag-checkered" style="color: #388e3c;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Completed</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['completed'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Completed orders</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-icon" style="background-color: #ffebee;">
                <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
            </div>
            <div class="card-content">
                <h3><span class="wi-highlight">Cancelled</span></h3>
                <div style="font-size: 2rem; font-weight: bold; color: #000;">{{ $stats['cancelled'] }}</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 4px;">Cancelled orders</div>
            </div>
        </div>
    </div>

    <div class="module-header">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Order
        </a>
    </div>

    <div class="table-container">
        <table id="quotesTable">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Quotation #</th>
                    <th>Customer</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->quote->quotation_number }}</td>
                        <td>{{ $order->quote->customer->company_name }}</td>
                        <td>{{ $order->order_date ? $order->order_date : 'N/A' }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge {{ 
                                $order->status === 'completed' ? 'badge-success' : 
                                ($order->status === 'confirmed' ? 'badge-info' : 
                                ($order->status === 'production' ? 'badge-warning' : 
                                ($order->status === 'cancelled' ? 'badge-danger' : 'badge-secondary'))) 
                            }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-secondary"> <i class="fas fa-eye"></i> </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.card-content {
    flex: 1;
}

.card-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.wi-highlight {
    color: #555;
    font-weight: 600;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.table-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
}

</style>

<script>
    $(document).ready(function() {
        $('#quotesTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            searching: true
        });
    });
</script>
@endsection
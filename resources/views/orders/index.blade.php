<!-- ==================== ORDERS INDEX VIEW ==================== -->
<!-- resources/views/orders/index.blade.php -->

@extends('layouts.app')
@section('title', 'Orders')
@section('page_title', 'Orders Management')

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Total Orders</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">All orders</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Pending</h6>
                        <div class="h4 fw-bold">{{ $stats['pending'] }}</div>
                        <small class="text-muted">Awaiting confirmation</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Confirmed</h6>
                        <div class="h4 fw-bold">{{ $stats['confirmed'] }}</div>
                        <small class="text-muted">Confirmed orders</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff8e1; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-industry" style="color: #ffa000;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">In Production</h6>
                        <div class="h4 fw-bold">{{ $stats['production'] }}</div>
                        <small class="text-muted">Currently in production</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-flag-checkered" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Completed</h6>
                        <div class="h4 fw-bold">{{ $stats['completed'] }}</div>
                        <small class="text-muted">Completed orders</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #ffebee; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Cancelled</h6>
                        <div class="h4 fw-bold">{{ $stats['cancelled'] }}</div>
                        <small class="text-muted">Cancelled orders</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Orders List</h2>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Order
        </a>
    </div>

    <div class="table-responsive">
        <table id="quotesTable" class="table table-striped">
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
                        <td>{{ isset($order->quote->quotation_number) ? $order->quote->quotation_number : '' }}</td>
                        <td>{{ isset($order->customer->company_name) ? $order->customer->company_name : '' }}</td>
                        <td>{{ $order->order_date ? $order->order_date : 'N/A' }}</td>
                        <td>{{ priceFormat($order->total_amount, 2) }}</td>
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
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                  
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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
<!-- ==================== ORDERS INDEX VIEW ==================== -->
<!-- resources/views/orders/index.blade.php -->

@extends('layouts.app')
@section('title', __('orders.title'))
@section('page_title', __('orders.page_title'))

@section('content')
<div class="content">
    <!-- Stats Cards with Bootstrap Grid -->
    <div class="row mb-4">
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e3f2fd; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart" style="color: #1976d2;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('orders.stats.total_orders') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">{{ __('orders.stats.all_orders') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff3e0; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #f57c00;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('orders.stats.pending') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['pending'] }}</div>
                        <small class="text-muted">{{ __('orders.stats.awaiting_confirmation') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e1f5fe; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #0288d1;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('orders.stats.confirmed') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['confirmed'] }}</div>
                        <small class="text-muted">{{ __('orders.stats.confirmed_orders') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #fff8e1; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-industry" style="color: #ffa000;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('orders.stats.production') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['production'] }}</div>
                        <small class="text-muted">{{ __('orders.stats.currently_in_production') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #e8f5e9; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-flag-checkered" style="color: #388e3c;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('orders.stats.completed') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['completed'] }}</div>
                        <small class="text-muted">{{ __('orders.stats.completed_orders') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3" style="background-color: #ffebee; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle" style="color: #d32f2f;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">{{ __('orders.stats.cancelled') }}</h6>
                        <div class="h4 fw-bold">{{ $stats['cancelled'] }}</div>
                        <small class="text-muted">{{ __('orders.stats.cancelled_orders') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ __('orders.list.title') }}</h2>
        @if(Auth::user()->can('Create Orders'))
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('orders.buttons.create') }}
        </a>
        @endif
    </div>

    <div class="table-responsive">
        <table id="quotesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>{{ __('orders.list.order_number') }}</th>
                    <th>{{ __('orders.list.quotation_number') }}</th>
                    <th>{{ __('orders.list.customer') }}</th>
                    <th>{{ __('orders.list.order_date') }}</th>
                    <th>{{ __('orders.list.total_amount') }}</th>
                    <th>{{ __('orders.list.status') }}</th>
                    <th>{{ __('orders.list.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ isset($order->quote->quotation_number) ? $order->quote->quotation_number : '' }}</td>
                        <td>{{ isset($order->customer->company_name) ? $order->customer->company_name : '' }}</td>
                        <td>{{ $order->order_date ? $order->order_date : __('orders.list.not_available') }}</td>
                        <td>{{ priceFormat($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge {{ 
                                $order->status === 'completed' ? 'badge-success' : 
                                ($order->status === 'confirmed' ? 'badge-info' : 
                                ($order->status === 'production' ? 'badge-warning' : 
                                ($order->status === 'cancelled' ? 'badge-danger' : 'badge-secondary'))) 
                            }}">
                                {{ ucfirst(__('orders.status.' . $order->status)) }}
                            </span>
                        </td>
                        <td>
                            @if(Auth::user()->can('Edit Orders'))
                            @if($order->status=='pending' || $order->status=='confirmed')
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-secondary" title="{{ __('orders.buttons.edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @endif
                            @if(Auth::user()->can('Delete Orders'))
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-secondary" title="{{ __('orders.buttons.view') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
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
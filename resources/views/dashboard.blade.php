@extends('layouts.app')

@section('title', __('dashboard.title'))
@section('page_title', __('dashboard.page_title'))

@section('content')
<div id="dashboard" class="module active">
    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Revenue Overview Card -->
        <div class="card">
            <h3>
                <i class="ti ti-cash"></i>
                <span class="wi-highlight">{{ __('dashboard.revenue_overview') }}</span>
            </h3>
            <div class="status-card status-production">
                <div>
                    <strong>{{ __('dashboard.total_revenue') }}</strong>
                    <div id="total-revenue-desc" style="font-size: 0.77rem; color: #666;">
                        {{ __('dashboard.total_revenue_desc') }}
                    </div>
                </div>
                <div id="total-revenue" style="font-size: 1.8rem; font-weight: bold; color: #000;">
                    {{ priceFormat($totalRevenue) }} 
                </div>
            </div>
            <div class="status-card status-pending">
                <div>
                    <strong>{{ __('dashboard.average_order_value') }}</strong>
                    <div id="avg-monthly-desc" style="font-size: 0.77rem; color: #666;">
                        {{ __('dashboard.average_order_value_desc') }}
                    </div>
                </div>
                <div id="avg-monthly" style="font-size: 1.8rem; font-weight: bold; color: #000;">
                    {{ priceFormat($averageOrderValue) }} 
                </div>
            </div>
            <div class="status-card status-completed">
                <div>
                    <strong>{{ __('dashboard.orders_this_month') }}</strong>
                    <div id="best-month-desc" style="font-size: 0.7rem; color: #666;">
                        {{ __('dashboard.orders_this_month_desc') }}
                    </div>
                </div>
                <div id="best-month" style="font-size: 2rem; font-weight: bold; color: #000;">
                    {{ $currentMonthOrders }}
                </div>
            </div>
        </div>

        <!-- Top Products by Value -->
        <div class="card">
            <h3>
                <i class="ti ti-star"></i>
                <span class="wi-highlight">{{ __('dashboard.top_products') }}</span>
            </h3>
            <div id="top-products-container">
                @forelse($topProducts as $product)
                <div class="product-item">
                    <div class="product-name">{{ $product->product_name }}</div>
                    <div class="product-amount">{{ number_format($product->total_amount, 2, '.', ',') }} DA</div>
                    <div class="progress-bar">
                        @php
                            $percentage = $topProducts->max('total_amount') > 0 ? 
                                ($product->total_amount / $topProducts->max('total_amount')) * 100 : 0;
                        @endphp
                        <div class="progress-fill" style="width: {{ $percentage }}%;"></div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #666; padding: 20px;">
                    {{ __('dashboard.no_product_data') }}
                </div>
                @endforelse
            </div>
        </div>

        <!-- Orders Overview Chart -->
        <div class="card">
            <h3>
                <i class="ti ti-chart-bar"></i>
                <span class="wi-highlight">{{ __('dashboard.orders_overview') }}</span>
            </h3>
            <div id="monthly-chart-container">
                <canvas id="ordersChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="card">
            <h3>
                <i class="ti ti-building"></i>
                <span class="wi-highlight">{{ __('dashboard.top_clients') }}</span>
            </h3>
            <div id="top-clients-container">
                @forelse($topClients as $client)
                <div class="client-item">
                    <div class="client-name">{{ $client->company_name ?: $client->contact_person }}</div>
                    <div class="client-amount">{{ priceFormat($client->total_spent) }} </div>
                    <div class="progress-bar">
                        @php
                            $percentage = $topClients->max('total_spent') > 0 ? 
                                ($client->total_spent / $topClients->max('total_spent')) * 100 : 0;
                        @endphp
                        <div class="progress-fill" style="width: {{ $percentage }}%;"></div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #666; padding: 20px;">
                    {{ __('dashboard.no_client_data') }}
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="ti ti-receipt-2"></i> {{ __('dashboard.recent_orders') }}</h3>
            <button class="btn btn-primary" onclick="showModule('orders')">
                {{ __('dashboard.view_all') }}
            </button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('dashboard.order_id') }}</th>
                    <th>{{ __('dashboard.customer') }}</th>
                    <th>{{ __('dashboard.total_amount') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
                    <th>{{ __('dashboard.order_date') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>
            <tbody id="recent-orders-tbody">
                @forelse($recentOrders as $order)
                <tr>
                    <td>
                        <strong>{{ $order->order_number }}</strong>
                    </td>
                    <td>
                        @php
                            $customer = \App\Models\Customer::find($order->customer_id);
                        @endphp
                        {{ $customer ? ($customer->company_name ?: $customer->contact_person) : __('dashboard.na') }}
                    </td>
                    <td>{{ priceFormat($order->total_amount) }} </td>
                    <td>
                        <span class="badge badge-{{ 
                            $order->status === 'completed' ? 'success' : 
                            ($order->status === 'pending' ? 'warning' : 
                            ($order->status === 'production' ? 'info' : 'secondary'))
                        }}">
                            @if($order->status === 'completed')
                                {{ __('dashboard.status_completed') }}
                            @elseif($order->status === 'pending')
                                {{ __('dashboard.status_pending') }}
                            @elseif($order->status === 'production')
                                {{ __('dashboard.status_production') }}
                            @else
                                {{ __('dashboard.status_other') }}
                            @endif
                        </span>
                    </td>
                    <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : __('dashboard.na') }}</td>
                    <td>
                        <button class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.66rem;" onclick="viewOrder('{{ $order->id }}')">
                            {{ __('dashboard.view') }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                        {{ __('dashboard.no_recent_orders') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
/* Your existing CSS styles remain the same */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* ... rest of your CSS styles ... */
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Orders chart data from PHP
const ordersChartData = {
    labels: {!! json_encode($ordersChart['labels']) !!},
    datasets: [{
        label: '{{ __('dashboard.orders_count') }}',
        data: {!! json_encode($ordersChart['data']) !!},
        backgroundColor: 'rgba(20, 54, 25, 0.2)',
        borderColor: 'rgb(20, 54, 25)',
        borderWidth: 2,
        tension: 0.3
    }]
};

// Initialize the chart when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ctx, {
        type: 'line',
        data: ordersChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '{{ __('dashboard.orders_count') }}: ' + context.raw;
                        }
                    }
                }
            }
        }
    });
});

function viewOrder(id) {
    window.location.href = '/orders/' + id;
}

function showModule(moduleId) {
    window.location.href = '/' + moduleId;
}
</script>
@endsection
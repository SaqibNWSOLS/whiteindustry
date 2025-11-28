@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div id="dashboard" class="module active">
    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Revenue Overview Card -->
        <div class="card">
            <h3>
                <i class="ti ti-cash"></i>
                <span class="wi-highlight">Revenue Overview</span>
            </h3>
            <div class="status-card status-production">
                <div>
                    <strong>Total Revenue</strong>
                    <div id="total-revenue-desc" style="font-size: 0.77rem; color: #666;">All completed orders</div>
                </div>
                <div id="total-revenue" style="font-size: 1.8rem; font-weight: bold; color: #000;">
                    {{ priceFormat($totalRevenue) }} 
                </div>
            </div>
            <div class="status-card status-pending">
                <div>
                    <strong>Average Order Value</strong>
                    <div id="avg-monthly-desc" style="font-size: 0.77rem; color: #666;">Based on completed orders</div>
                </div>
                <div id="avg-monthly" style="font-size: 1.8rem; font-weight: bold; color: #000;">
                    {{ priceFormat($averageOrderValue) }} 
                </div>
            </div>
            <div class="status-card status-completed">
                <div>
                    <strong>Orders This Month</strong>
                    <div id="best-month-desc" style="font-size: 0.7rem; color: #666;">Current month performance</div>
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
                <span class="wi-highlight">Top Products</span>
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
                    No product data available
                </div>
                @endforelse
            </div>
        </div>

        <!-- Orders Overview Chart -->
        <div class="card">
            <h3>
                <i class="ti ti-chart-bar"></i>
                <span class="wi-highlight">Orders Overview</span>
            </h3>
            <div id="monthly-chart-container">
                <canvas id="ordersChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="card">
            <h3>
                <i class="ti ti-building"></i>
                <span class="wi-highlight">Top Clients</span>
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
                    No client data available
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="ti ti-receipt-2"></i> Recent Orders</h3>
            <button class="btn btn-primary" onclick="showModule('orders')">View All</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
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
                        {{ $customer ? ($customer->company_name ?: $customer->contact_person) : 'N/A' }}
                    </td>
                    <td>{{ priceFormat($order->total_amount) }} </td>
                    <td>
                        <span class="badge badge-{{ 
                            $order->status === 'completed' ? 'success' : 
                            ($order->status === 'pending' ? 'warning' : 
                            ($order->status === 'production' ? 'info' : 'secondary'))
                        }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>
                    <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <button class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.66rem;" onclick="viewOrder('{{ $order->id }}')">
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                        No recent orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 10px;
    border-bottom: 1px solid #eee;
}

.status-card:last-child {
    border-bottom: none;
}

.product-item, .client-item {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.product-item:last-child, .client-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.product-name, .client-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.product-amount, .client-amount {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 8px;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background-color: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background-color: rgb(20, 54, 25);
    border-radius: 4px;
    transition: width 0.3s ease;
}

#monthly-chart-container {
    height: 200px;
    margin-top: 10px;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.table-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    background-color: #f9f9f9;
    font-weight: 600;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-primary {
    background-color: rgb(20, 54, 25);
    color: white;
}

.btn-primary:hover {
    background-color: rgb(15, 44, 20);
}

.btn-secondary {
    background-color: #e0e0e0;
    color: #333;
}

.btn-secondary:hover {
    background-color: #d0d0d0;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-success {
    background-color: #d4edda;
    color: #155724;
}

.badge-warning {
    background-color: #fff3cd;
    color: #856404;
}

.badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.badge-secondary {
    background-color: #e2e3e5;
    color: #383d41;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Orders chart data from PHP
const ordersChartData = {
    labels: {!! json_encode($ordersChart['labels']) !!},
    datasets: [{
        label: 'Orders Count',
        data: {!! json_encode($ordersChart['data']) !!},
        backgroundColor: 'rgba(20, 54, 25, 0.2)',
        borderColor: 'rgb(20, 54, 25)',
        borderWidth: 2,
        tension: 0.3
    }]
};

// Initialize the chart when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Create orders chart
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
                            return 'Orders: ' + context.raw;
                        }
                    }
                }
            }
        }
    });
});

function viewOrder(id) {
    // Navigate to order details
    window.location.href = '/orders/' + id;
}

function showModule(moduleId) {
    // This function would handle navigation to other modules
    console.log('Navigating to module:', moduleId);
    // Implementation would depend on your routing structure
    window.location.href = '/' + moduleId;
}
</script>
@endsection
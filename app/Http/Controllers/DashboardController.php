<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Customer;
use App\Models\Production;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Revenue from completed orders
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount') ?? 0;
        
        // Average Order Value
        $completedOrdersCount = Order::where('status', 'completed')->count();
        $averageOrderValue = $completedOrdersCount > 0 ? $totalRevenue / $completedOrdersCount : 0;
        
        // Current Month Orders
        $currentMonthOrders = Order::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        
        // Top Products by total amount
        $topProducts = OrderProduct::selectRaw('
                product_name,
                SUM(total_amount) as total_amount,
                COUNT(*) as order_count
            ')
            ->groupBy('product_name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();
        
        // Top Clients
        $topClients = Customer::selectRaw('
                customers.id,
                customers.company_name,
                customers.contact_person,
                COALESCE(SUM(orders.total_amount), 0) as total_spent,
                COUNT(orders.id) as order_count
            ')
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.status', 'completed')
            ->groupBy('customers.id', 'customers.company_name', 'customers.contact_person')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
        
        // Recent Orders
        $recentOrders = Order::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Orders chart data - last 6 months
        $ordersChart = $this->getOrdersChartData();
        
        return view('dashboard', compact(
            'totalRevenue',
            'averageOrderValue',
            'currentMonthOrders',
            'topProducts',
            'topClients',
            'recentOrders',
            'ordersChart'
        ));
    }
    
    private function getOrdersChartData()
    {
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $orderCount = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data[] = $orderCount;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
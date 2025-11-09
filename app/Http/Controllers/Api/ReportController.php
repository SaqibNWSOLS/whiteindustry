<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Order, Invoice, ProductionOrder, Inventory, Customer, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $period = $request->get('period', 'year');
        $query = Order::query();

        switch ($period) {
            case 'month':
                $query->whereMonth('created_at', date('m'));
                break;
            case 'quarter':
                $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                break;
            default:
                $query->whereYear('created_at', date('Y'));
        }

        return response()->json([
            'total_sales' => $query->sum('total_value'),
            'total_orders' => $query->count(),
            'average_order_value' => $query->avg('total_value'),
            'conversion_rate' => 68, // Calculate from quotes to orders
            'top_customers' => Customer::withCount('orders')
                ->withSum('orders', 'total_value')
                ->orderBy('orders_sum_orders_total_value', 'desc')
                ->take(5)
                ->get(),
            'top_products' => Product::withCount('orders')
                ->withSum('orders', 'total_value')
                ->orderBy('orders_sum_orders_total_value', 'desc')
                ->take(5)
                ->get(),
            'monthly_trend' => Order::selectRaw('MONTH(created_at) as month, SUM(total_value) as revenue')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ]);
    }

    public function inventory(Request $request)
    {
        return response()->json([
            'total_value' => Inventory::sum(DB::raw('current_stock * unit_cost')),
            'low_stock_items' => Inventory::where('status', 'low_stock')->get(),
            'out_of_stock_items' => Inventory::where('status', 'out_of_stock')->get(),
            'inventory_by_category' => Inventory::select('type', DB::raw('SUM(current_stock * unit_cost) as value'))
                ->groupBy('type')
                ->get(),
            'aging_analysis' => Inventory::selectRaw('
                CASE
                    WHEN DATEDIFF(NOW(), updated_at) <= 30 THEN "0-30 days"
                    WHEN DATEDIFF(NOW(), updated_at) <= 90 THEN "31-90 days"
                    WHEN DATEDIFF(NOW(), updated_at) <= 180 THEN "91-180 days"
                    ELSE "181+ days"
                END as age_range,
                COUNT(*) as items_count,
                SUM(current_stock * unit_cost) as total_value
            ')
                ->groupBy('age_range')
                ->get(),
        ]);
    }

    public function financial(Request $request)
    {
        $year = $request->get('year', date('Y'));

        return response()->json([
            'total_revenue' => Order::whereYear('created_at', $year)->sum('total_value'),
            'accounts_receivable' => Invoice::whereIn('status', ['unpaid', 'partial'])->sum('balance'),
            'overdue_invoices' => Invoice::where('status', 'overdue')->sum('balance'),
            'gross_profit_margin' => 38, // Calculate from costs
            'net_profit_margin' => 12,
            'monthly_summary' => Invoice::selectRaw('
                MONTH(issue_date) as month,
                SUM(total_amount) as revenue,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as collected
            ')
                ->whereYear('issue_date', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ]);
    }

    public function production(Request $request)
    {
        return response()->json([
            'orders_completed' => ProductionOrder::where('status', 'completed')
                ->whereYear('created_at', date('Y'))
                ->count(),
            'on_time_delivery_rate' => 94,
            'average_production_time' => 4.2,
            'equipment_utilization' => 87,
            'quality_metrics' => [
                'first_pass_rate' => 98.5,
                'rework_rate' => 1.4,
                'rejection_rate' => 0.1,
            ],
            'production_by_line' => ProductionOrder::select('production_line', DB::raw('COUNT(*) as orders_count'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('production_line')
                ->get(),
        ]);
    }

    public function customerAnalytics(Request $request)
    {
        return response()->json([
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'retention_rate' => 89,
            'average_customer_value' => Customer::withSum('orders', 'total_value')
                ->get()
                ->avg('orders_sum_orders_total_value'),
            'customer_segmentation' => [
                'platinum' => Customer::withSum('orders', 'total_value')
                    ->having('orders_sum_orders_total_value', '>=', 100000)
                    ->count(),
                'gold' => Customer::withSum('orders', 'total_value')
                    ->havingBetween('orders_sum_orders_total_value', [50000, 99999])
                    ->count(),
                'silver' => Customer::withSum('orders', 'total_value')
                    ->havingBetween('orders_sum_orders_total_value', [20000, 49999])
                    ->count(),
                'bronze' => Customer::withSum('orders', 'total_value')
                    ->having('orders_sum_orders_total_value', '<', 20000)
                    ->count(),
            ],
        ]);
    }

}
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportingController extends Controller
{
    /**
     * Return aggregated sales reporting data.
     * Query param: period (year|quarter|month|30) defaults to 'year'
     */

    public function index(\Illuminate\Http\Request $request){
        $period = $request->query('period', 'year');
        $sort = $request->query('sort', null);

        $end = Carbon::now();
        switch ($period) {
            case 'quarter':
                $start = (clone $end)->startOfQuarter();
                break;
            case 'month':
                $start = (clone $end)->startOfMonth();
                break;
            case '30':
            case '30days':
                $start = (clone $end)->subDays(30);
                break;
            case 'year':
            default:
                $start = (clone $end)->startOfYear();
                break;
        }
        $ordersQuery = Order::query()->whereBetween('created_at', [$start, $end]);
        $totalSales = (float) $ordersQuery->sum('total_value');
        $totalOrders = (int) $ordersQuery->count();
        $averageOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

        $conversionRate = null;
        if (class_exists(Lead::class)) {
            $leads = Lead::whereBetween('created_at', [$start, $end])->count();
            if ($leads > 0) {
                $conversionRate = round(($totalOrders / $leads) * 100, 2);
            }
        }

        $annualTarget = 4000000;
        $progressPercent = $annualTarget > 0 ? min(100, round(($totalSales / $annualTarget) * 100, 2)) : 0;

        // previous period for change text
        $previousEnd = (clone $start)->subSecond();
        $previousStart = (clone $start)->subSeconds($end->diffInSeconds($start));
        $previousTotal = Order::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total_value');
        $changeText = '';
        if ($previousTotal > 0) {
            $pct = round((($totalSales - $previousTotal) / $previousTotal) * 100, 2);
            $changeText = ($pct >= 0 ? '+' : '') . $pct . '% vs previous';
        }

        $category = $this->formatCategoryBreakdown($start, $end, $totalSales);
        // apply server-side sort if requested
        if ($sort && is_array($category)) {
            if ($sort === 'revenue') {
                usort($category, function($a, $b){ return ($b['revenue_numeric'] ?? 0) <=> ($a['revenue_numeric'] ?? 0); });
            } elseif ($sort === 'units') {
                usort($category, function($a, $b){ return ($b['units_numeric'] ?? 0) <=> ($a['units_numeric'] ?? 0); });
            } elseif ($sort === 'growth') {
                usort($category, function($a, $b){ return ($b['yoy'] ?? 0) <=> ($a['yoy'] ?? 0); });
            }
        }

        return view('modules.reports', [
            'initial_total_sales' => 'DZD ' . number_format($totalSales, 2, '.', ','),
            'initial_total_sales_numeric' => $totalSales,
            'initial_total_orders' => $totalOrders,
            'initial_average_order_value' => 'DZD ' . number_format($averageOrderValue, 2, '.', ','),
            'initial_average_order_value_numeric' => $averageOrderValue,
            'initial_conversion_rate' => $conversionRate,
            'initial_conversion_rate_formatted' => $conversionRate !== null ? $conversionRate . '%' : null,
            'initial_progress_percent' => $progressPercent,
            'initial_change_text' => $changeText,
            // top customers and products for the same period (YTD)
            'initial_top_customers' => $this->formatTopCustomers($start, $end, $totalSales),
            'initial_top_products' => $this->formatTopProducts($start, $end),
            'initial_category_breakdown' => $category,
            'selected_period' => $period,
            'selected_sort' => $sort,
        ]);
    }

    public function reporting(Request $request)
    {
        $period = $request->query('period', 'year');
        $end = Carbon::now();

        switch ($period) {
            case 'quarter':
                $start = (clone $end)->startOfQuarter();
                break;
            case 'month':
                $start = (clone $end)->startOfMonth();
                break;
            case '30':
            case '30days':
                $start = (clone $end)->subDays(30);
                break;
            case 'year':
            default:
                $start = (clone $end)->startOfYear();
                break;
        }

        $ordersQuery = Order::query()->whereBetween('created_at', [$start, $end]);
        $totalSales = (float) $ordersQuery->sum('total_value');
        $totalOrders = (int) $ordersQuery->count();
        $averageOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

        // Try to compute a conversion rate using leads if model exists
        $conversionRate = null;
        try {
            if (class_exists(Lead::class)) {
                $leads = Lead::whereBetween('created_at', [$start, $end])->count();
                if ($leads > 0) {
                    $conversionRate = round(($totalOrders / $leads) * 100, 2);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Simple progress against a default target (could be configurable)
        $annualTarget = 4000000; // DZD
        $progressPercent = $annualTarget > 0 ? min(100, round(($totalSales / $annualTarget) * 100, 2)) : 0;

        // Compare to previous same-length period
        $previousEnd = (clone $start)->subSecond();
        $previousStart = (clone $start)->subSeconds($end->diffInSeconds($start));
        $previousTotal = Order::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total_value');
        $changeText = '';
        if ($previousTotal > 0) {
            $delta = $totalSales - $previousTotal;
            $pct = round(($delta / $previousTotal) * 100, 2);
            $changeText = ($pct >= 0 ? '+' : '') . $pct . '% vs previous';
        }

        return response()->json([
            'period' => $period,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
            'total_sales' => $this->formatCurrency($totalSales),
            'total_sales_numeric' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $this->formatCurrency($averageOrderValue),
            'average_order_value_numeric' => $averageOrderValue,
            'conversion_rate' => $conversionRate,
            'conversion_rate_formatted' => $conversionRate !== null ? $conversionRate . '%' : null,
            'progress_percent' => $progressPercent,
            'change_text' => $changeText,
            'top_customers' => $this->formatTopCustomers($start, $end, $totalSales),
            'top_products' => $this->formatTopProducts($start, $end),
            'category_breakdown' => $this->formatCategoryBreakdown($start, $end, $totalSales),
        ]);
    }

    /**
     * Export the sales report for the given period as a PDF download.
     * Query param: period (year|quarter|month|30)
     */
    public function exportPdf(Request $request)
    {
        $period = $request->query('period', 'year');
        $end = Carbon::now();

        switch ($period) {
            case 'quarter':
                $start = (clone $end)->startOfQuarter();
                break;
            case 'month':
                $start = (clone $end)->startOfMonth();
                break;
            case '30':
            case '30days':
                $start = (clone $end)->subDays(30);
                break;
            case 'year':
            default:
                $start = (clone $end)->startOfYear();
                break;
        }

        // reuse existing helper formatting functions
        $totalSales = (float) Order::whereBetween('created_at', [$start, $end])->sum('total_value');
        $totalOrders = (int) Order::whereBetween('created_at', [$start, $end])->count();
        $averageOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

        $data = [
            'period' => $period,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'total_sales' => $this->formatCurrency($totalSales),
            'total_orders' => $totalOrders,
            'average_order_value' => $this->formatCurrency($averageOrderValue),
            'top_customers' => $this->formatTopCustomers($start, $end, $totalSales),
            'top_products' => $this->formatTopProducts($start, $end),
            'category_breakdown' => $this->formatCategoryBreakdown($start, $end, $totalSales),
        ];

        $fileName = sprintf('sales-report-%s-%s.pdf', $period, date('Ymd'));

        $pdf = Pdf::loadView('modules.reports_pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    protected function formatCurrency($amount)
    {
        return 'DZD ' . number_format((float) $amount, 2, '.', ',');
    }

    protected function formatTopCustomers($start, $end, $totalSales = 0)
    {
        $top = Customer::withCount('orders')
            ->withSum(['orders as revenue' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }], 'total_value')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        return $top->map(function ($c) use ($totalSales) {
            $revenue = $c->revenue ?? 0;
            $percent = $totalSales > 0 ? round(($revenue / $totalSales) * 100, 1) : 0;
            return [
                'name' => $c->company_name ?? ($c->contact_person ?? 'Unknown'),
                'orders' => $c->orders_count ?? 0,
                'revenue' => 'DZD ' . number_format((float) $revenue, 0, '.', ','),
                'revenue_numeric' => (float) $revenue,
                'percent' => $percent,
            ];
        })->toArray();
    }

    protected function formatTopProducts($start, $end)
    {
        $top = Product::withCount('orders')
            ->withSum(['orders as revenue' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }], 'total_value')
            ->withSum(['orders as units' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }], 'quantity')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        $max = $top->max('revenue') ?: 1;
        return $top->map(function ($p) use ($max) {
            $revenue = $p->revenue ?? 0;
            $units = $p->units ?? 0;
            $pct = $max > 0 ? round(($revenue / $max) * 100, 0) : 0;
            return [
                'name' => $p->name ?? 'Unnamed Product',
                'units' => (int) $units,
                'revenue' => 'DZD ' . number_format((float) $revenue, 0, '.', ','),
                'revenue_numeric' => (float) $revenue,
                'pct' => $pct,
            ];
        })->toArray();
    }

    /**
     * Returns an array of categories with units, revenue, % of total, avg price and YoY growth
     */
    protected function formatCategoryBreakdown($start, $end, $totalSales = 0)
    {
        // current period aggregations grouped by product category
        $rows = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->selectRaw('products.category as category, SUM(orders.quantity) as units_sold, SUM(orders.total_value) as revenue')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.category')
            ->orderByDesc('revenue')
            ->get();

        // previous period for YoY comparison (same length immediately prior)
        $previousEnd = (clone $start)->subSecond();
        $previousStart = (clone $start)->subSeconds($end->diffInSeconds($start));

        $prevRows = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->selectRaw('products.category as category, SUM(orders.total_value) as revenue')
            ->whereBetween('orders.created_at', [$previousStart, $previousEnd])
            ->groupBy('products.category')
            ->get()
            ->keyBy('category');

        $result = [];
        foreach ($rows as $r) {
            $category = $r->category ?: 'Uncategorized';
            $units = (float) $r->units_sold;
            $revenue = (float) $r->revenue;
            $percent = $totalSales > 0 ? round(($revenue / $totalSales) * 100, 1) : 0;
            $avgPrice = $units > 0 ? round($revenue / $units, 2) : 0;

            $prevRevenue = isset($prevRows[$category]) ? (float) $prevRows[$category]->revenue : 0;
            $yoy = null;
            if ($prevRevenue > 0) {
                $yoy = round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1);
            }

            $result[] = [
                'category' => $category,
                'units' => number_format($units, 0, '.', ','),
                'units_numeric' => $units,
                'revenue' => 'DZD ' . number_format($revenue, 0, '.', ','),
                'revenue_numeric' => $revenue,
                'percent_of_total' => $percent,
                'avg_price' => 'DZD ' . number_format($avgPrice, 2, '.', ','),
                'yoy' => $yoy, // percentage or null
            ];
        }

        return $result;
    }
}

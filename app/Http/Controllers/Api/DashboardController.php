<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Order, Invoice, Product, Customer, ProductionOrder, Inventory, Quote};

class DashboardController extends Controller
{
    // Returns a structured summary for the dashboard widgets
    public function index(Request $request)
    {
        $production = [
            'active_orders' => ProductionOrder::whereIn('status', ['pending', 'mixing', 'filling', 'packaging'])->count(),
            'pending_launch' => ProductionOrder::where('status', 'pending')->count(),
            'completed_today' => ProductionOrder::where('status', 'completed')->whereDate('updated_at', today())->count(),
        ];

        // compute inventory percentages per type (raw, packaging, final)
        $rawStock = (float) Inventory::where('type', 'raw')->sum('current_stock');
        $rawMin = (float) Inventory::where('type', 'raw')->sum('minimum_stock');
        $packStock = (float) Inventory::where('type', 'packaging')->sum('current_stock');
        $packMin = (float) Inventory::where('type', 'packaging')->sum('minimum_stock');
        $finStock = (float) Inventory::where('type', 'final')->sum('current_stock');
        $finMin = (float) Inventory::where('type', 'final')->sum('minimum_stock');

        $rawPct = $rawStock + $rawMin > 0 ? round(($rawStock / ($rawStock + $rawMin)) * 100) : 0;
        $packPct = $packStock + $packMin > 0 ? round(($packStock / ($packStock + $packMin)) * 100) : 0;
        $finPct = $finStock + $finMin > 0 ? round(($finStock / ($finStock + $finMin)) * 100) : 0;

        $inventory = [
            'raw_materials_pct' => $rawPct,
            'packaging_pct' => $packPct,
            'finished_products_pct' => $finPct,
            'low_stock_alerts' => Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
        ];

        // simple sales summary
        $sales = [
            'monthly_revenue' => Order::whereMonth('created_at', date('m'))->sum('total_value'),
            'active_quotes' => Quote::where('status', 'pending')->count(),
            'pending_payments' => Invoice::whereIn('status', ['unpaid', 'partial'])->sum('balance'),
            'new_customers' => Customer::whereYear('created_at', date('Y'))->count(),
        ];

        $recent_orders = Order::with(['customer', 'product'])->latest()->take(10)->get()->map(function ($o) {
            return [
                'order_number' => $o->order_number ?? $o->id,
                'customer' => $o->customer ? $o->customer->company_name : null,
                'product' => $o->product ? $o->product->name : null,
                'quantity' => (string) ($o->quantity ?? '' ) . ' ' . ($o->unit ?? ''),
                'status' => $o->status,
                'created_at' => $o->created_at ? $o->created_at->toDateTimeString() : null,
            ];
        });

        return response()->json(compact('production', 'inventory', 'sales', 'recent_orders'));
    }

    public function recentOrders(Request $request)
    {
        $orders = Order::with('customer', 'product')->latest()->take(10)->get();
        return response()->json(['data' => $orders]);
    }

    public function recentInvoices(Request $request)
    {
        $invoices = Invoice::with('customer')->latest()->take(10)->get();
        return response()->json(['data' => $invoices]);
    }

    public function salesReport(Request $request)
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
        ]);
    }

    public function recentProducts(Request $request)
    {
        $products = Product::latest()->take(10)->get()->map(function ($p) {
            return [
                'product_code' => $p->product_code,
                'name' => $p->name,
                'category' => $p->category,
                'unit_price' => (string) $p->unit_price,
                'status' => $p->status,
            ];
        });

        return response()->json(['data' => $products]);
    }

    public function recentInventory(Request $request)
    {
        $items = Inventory::latest()->take(10)->get()->map(function ($i) {
            return [
                'material_code' => $i->material_code,
                'name' => $i->name,
                'category' => $i->category,
                'current_stock' => (string) $i->current_stock,
                'minimum_stock' => (string) $i->minimum_stock,
                'unit' => $i->unit,
                'status' => $i->status,
            ];
        });

        return response()->json(['data' => $items]);
    }
}
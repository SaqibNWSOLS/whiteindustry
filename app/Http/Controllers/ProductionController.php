<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::with(['order.quote.customer', 'invoices', 'items'])->latest()->get();
        
        $stats = [
            'total' => Production::count(),
            'pending' => Production::where('status', 'pending')->count(),
            'in_progress' => Production::where('status', 'in_progress')->count(),
            'quality_check' => Production::where('status', 'quality_check')->count(),
            'completed' => Production::where('status', 'completed')->count(),
            'with_invoices' => Production::has('invoices')->count(),
        ];
        return view('production.index', compact('productions', 'stats'));
    }

    public function create()
    {
        $confirmedOrders = Order::where('status', 'confirmed')
            ->with(['quote.customer', 'products'])
            ->get();
        return view('production.create', compact('confirmedOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'production_notes' => 'nullable|string',
            'production_items' => 'required|array',
            'production_items.*.order_product_id' => 'required|exists:order_products,id',
            'production_items.*.quantity_planned' => 'required|min:1',
        ]);

        $production = Production::create([
            'production_number' => 'PROD-' . Str::random(8),
            'order_id' => $request->order_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'production_notes' => $request->production_notes,
            'status' => 'pending'
        ]);

        // Create production items
        foreach ($request->production_items as $item) {
            ProductionItem::create([
                'production_id' => $production->id,
                'order_product_id' => $item['order_product_id'],
                'quantity_planned' => $item['quantity_planned'],
                'quantity_produced' => 0,
                'status' => 'pending'
            ]);
        }

        $order = Order::find($request->order_id);
        $order->update(['status' => 'production']);

        return redirect()->route('production.show', $production->id)
            ->with('success', 'Production created successfully');
    }

    public function show($id)
    {
        $production = Production::with([
            'order.quote.customer',
            'order.products.items',
            'items.orderProduct',
            'invoices'
        ])->findOrFail($id);

        return view('production.show', compact('production'));
    }

    public function updateProductionItem(Request $request, $id)
    {
        $request->validate([
            'quantity_produced' => 'required|integer|min:0',
            'status' => 'required|in:pending,in_progress,completed,quality_check',
            'notes' => 'nullable|string'
        ]);

        $productionItem = ProductionItem::findOrFail($id);
        $productionItem->update([
            'quantity_produced' => $request->quantity_produced,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Production item updated');
    }

    public function startProduction($id)
    {
        $production = Production::findOrFail($id);
        $production->update(['status' => 'in_progress']);
        return redirect()->back()->with('success', 'Production started');
    }

    public function completeProduction($id)
    {
        $production = Production::findOrFail($id);
        $production->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Production completed');
    }
}
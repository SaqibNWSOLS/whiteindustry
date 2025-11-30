<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\InventoryTransaction;
use App\Models\Product;
use DB;

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

        $rndDocuments=$production?->order?->rndQuote?->documents??[];
        $qaDocuments=$production?->order?->qaQuote?->documents??[];

        return view('production.show', compact('production','rndDocuments','qaDocuments'));
    }

    public function addReadyQuantity(Request $request, $id)
{
    $request->validate([
        'quantity_to_add' => 'required|integer|min:1',
        'notes' => 'nullable|string'
    ]);

    $productionItem = ProductionItem::findOrFail($id);
    
    // Check if adding quantity exceeds planned quantity
    $newQuantity = $productionItem->quantity_produced + $request->quantity_to_add;
    if ($newQuantity > $productionItem->quantity_planned) {
        return redirect()->back()->with('error', 'Cannot exceed planned quantity of ' . $productionItem->quantity_planned);
    }

    DB::beginTransaction();

    try {
        // Increment the produced quantity
        $productionItem->increment('quantity_produced', $request->quantity_to_add);
        
        // Update status
        $productionItem->update([
            'status' => 'completed',
            'notes' => $request->notes
        ]);
         $product = Product::firstOrCreate(
            ['name' => $productionItem->orderProduct->product_name],
            ['minimum_stock' => 0, 'current_stock' => 0,'category'=>'final_product','product_code'=>Str::random(5),'unit_price'=>$productionItem->orderProduct->price_unit,'unit_of_measure'=>$productionItem->orderProduct->volume_unit]
        );

        $productionItem->update(['products_id'=>$product->id]);


        // Create inventory transaction record for the added quantity
        InventoryTransaction::create([
            'product_id' => $product->id,
            'production_item_id' => $productionItem->id,
            'transaction_type' => 'production',
            'quantity_change' => $request->quantity_to_add,
            'reference_type' => 'production_item',
            'reference_id' => $productionItem->id,
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'transaction_date' => now()
        ]);

        // Update or create inventory balance
       
        DB::commit();

        return redirect()->back()->with('success', 'Added ' . $request->quantity_to_add . ' units to production. Total produced: ' . $productionItem->quantity_produced);
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Failed to add quantity: ' . $e->getMessage());
    }
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
    
    // Check if production is already completed
    if ($production->status === 'completed') {
        return redirect()->back()->with('error', 'Production is already completed.');
    }
    
    // Check if production is fully fulfilled
    if (!$this->isProductionFulfilled($production)) {
        return redirect()->back()->with('error', 'Cannot complete production. Not all items are fully produced.');
    }
    
    // Update production and order status
    $production->update(['status' => 'completed']);
    $production->order->update(['status' => 'completed']);
    
    return redirect()->back()->with('success', 'Production completed successfully.');
}

/**
 * Check if production is fully fulfilled
 */
private function isProductionFulfilled(Production $production): bool
{
    // Check if all production items are fulfilled
    foreach ($production->items as $item) {
        if ($item->quantity_produced < $item->quantity_planned) {
            return false;
        }
    }
    
    return true;
}

public function inventoryHistory($id)
    {
        $production = Production::with([
            'inventoryTransactions.product',
            'inventoryTransactions.productionItem.orderProduct',
            'inventoryTransactions.createdBy'
        ])->findOrFail($id);

        return view('production.inventory-history', compact('production'));
    }

    /**
     * Show inventory transaction history for a specific production item
     */
    public function itemInventoryHistory($productionId, $itemId)
    {
        $productionItem = ProductionItem::with([
            'inventoryTransactions.product',
            'inventoryTransactions.createdBy',
            'orderProduct'
        ])->where('production_id', $productionId)
          ->where('id', $itemId)
          ->firstOrFail();

        $production = Production::findOrFail($productionId);

        return view('production.item-inventory-history', compact('productionItem', 'production'));
    }
    
}
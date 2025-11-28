<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QaQuote;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderProduct;

class OrderController extends Controller
{
    public function index()
{
    $orders = Order::with(['quote.customer', 'items', 'production'])->latest()->get();
    
    $stats = [
        'total' => Order::count(),
        'pending' => Order::where('status', 'pending')->count(),
        'confirmed' => Order::where('status', 'confirmed')->count(),
        'production' => Order::where('status', 'production')->count(),
        'completed' => Order::where('status', 'completed')->count(),
        'cancelled' => Order::where('status', 'cancelled')->count(),
    ];

    return view('orders.index', compact('orders', 'stats'));
}

    public function storeBasic(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
        ]);

        // Create the main quote
        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(8),
            'customer_id' => $request->customer_id,
            'order_notes' => $request->notes,
            'status' => 'confirmed'
        ]);

        // Store quote ID in session for next steps
        session(['current_order_id' => $order->id]);

        return redirect()->route('orders.create', ['step' => 'products'])
            ->with('success', 'Basic information saved successfully');
    }


     public function updateBasic(Request $request,$id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
        ]);


        $order=Order::where('id',$id)->first();

        // Create the main quote
        $order = $order->update([
            'customer_id' => $request->customer_id,
            'notes' => $request->notes,
            'status' => 'confirmed'
        ]);



        // Store quote ID in session for next steps
        session(['current_order_id' => $id]);

        return redirect()->route('orders.edit', ['order' => $id, 'step' => 'products']);
    }

   public function addProducts(Request $request, Order $order)
{
    $request->validate([
        'products' => 'required|array',
        'products.*.product_name' => 'required|string|max:255',
        'products.*.quantity' => 'required',
        'products.*.product_type' => 'required|in:cosmetic,food_supplement',
    ]);

    $existingProductIds = $order->products->pluck('id')->toArray();
    $newProductIds = [];

    foreach ($request->products as $productData) {
        if (isset($productData['id'])) {
            // ✅ Update existing product
            $quoteProduct = $order->products()->find($productData['id']);
            if ($quoteProduct) {
                $quoteProduct->update([
                    'product_name' => $productData['product_name'],
                    'product_type' => $productData['product_type'],
                    'quantity'=> $productData['quantity'],
                    'tax_rate' => 19.00,
                ]);
                $newProductIds[] = $quoteProduct->id;
                continue;
            }
        }

        // ✅ Create new product
        $newProduct = $order->products()->create([
            'product_name' => $productData['product_name'],
            'product_type' => $productData['product_type'],
            'quantity'=> $productData['quantity'],
            'tax_rate' => 19.00,
        ]);
        $newProductIds[] = $newProduct->id;
    }

    // ❌ Optional: Remove products not included in the request
    // $quote->products()->whereNotIn('id', $newProductIds)->delete();

    return redirect()->route('orders.edit', [
        'order' => $order->id,
        'step' => 'raw_materials'
    ]);
}

 public function addRawMaterialsAndBlends(Request $request, $id)
{


// First, check what data we actually have
$hasItemId = false;
$hasBlendId = false;

// Check for valid item_id in raw_materials
if (!empty($request->raw_materials)) {
    foreach ($request->raw_materials as $rawMaterial) {
        if (!empty($rawMaterial['materials']) && is_array($rawMaterial['materials'])) {
            foreach ($rawMaterial['materials'] as $material) {
                if (!empty($material['item_id'])) {
                    $hasItemId = true;
                    break 2;
                }
            }
        }
    }
}

// Check for valid blend_id in blends
if (!empty($request->blends)) {
    foreach ($request->blends as $blend) {
        if (!empty($blend['blend_id'])) {
            $hasBlendId = true;
            break;
        }
    }
}

// Validate mutual exclusion
if ($hasItemId && $hasBlendId) {
    return redirect()->back()->withErrors(['base' => 'Cannot provide both raw materials and blends.'])->withInput();
}

if (!$hasItemId && !$hasBlendId) {
    return redirect()->back()->withErrors(['base' => 'Either raw materials (with item_id) or blends (with blend_id) must be provided.'])->withInput();
}

// Now do the actual validation based on what we have
if ($hasItemId) {
    $request->validate([
        'raw_materials' => 'required|array',
        'raw_materials.*.materials' => 'required|array|min:1',
        'raw_materials.*.materials.*.item_id' => 'required|exists:products,id',
        'raw_materials.*.materials.*.percentage' => 'required|numeric|min:0.01|max:100',
    ]);
} elseif ($hasBlendId) {
    $request->validate([
        'blends' => 'required|array|min:1',
        'blends.*.blend_id' => 'required|exists:products,id',
    ]);
}


    $quote = Order::findOrFail($id);

    // Process Raw Materials (if provided)
    if (!empty($request->raw_materials)) {
        foreach ($request->raw_materials as $quoteProductId => $productMaterials) {
            $orderProduct = OrderProduct::find($quoteProductId);
            if (!$orderProduct) {
                continue;
            }

            $existingItemIds = $orderProduct->rawMaterialItems->pluck('id')->toArray();
            $newItemIds = [];
            $totalPercentage = 0;

            foreach ($productMaterials['materials'] as $materialData) {
                $material = Product::find($materialData['item_id']);
                $totalPercentage += $materialData['percentage'];

                if (isset($materialData['id']) && !empty($material)) {
                    // Update existing raw material
                    $quoteItem = $orderProduct->rawMaterialItems()->find($materialData['id']);
                    if ($quoteItem) {
                        $quoteItem->update([
                            'item_id' => $material->id,
                            'item_name' => $material->name,
                            'unit' => $material->unit_of_measure,
                            'unit_cost' => $material->unit_price,
                            'percentage' => $materialData['percentage'],
                            'quantity' => 0,
                            'total_cost' => $material->unit_price/100*$materialData['percentage'],
                        ]);
                        $newItemIds[] = $quoteItem->id;
                        continue;
                    }
                }

                // Create new raw material
                if (!empty($material)) {
                   
                $newItem = $orderProduct->rawMaterialItems()->create([
                    'item_type' => 'raw_material',
                    'item_id' => $material->id,
                    'item_name' => $material->name,
                    'quantity' => 0,
                    'unit' => $material->unit_of_measure,
                    'percentage' => $materialData['percentage'],
                    'unit_cost' => $material->unit_price,
                    'total_cost' => $material->unit_price/100*$materialData['percentage'],
                ]);
                $newItemIds[] = $newItem->id;
                 // code...
                }
            }

            // Optional: delete items not included in request
            $orderProduct->rawMaterialItems()->whereNotIn('id', $newItemIds)->delete();

            // Validate total percentage
            if (!empty($material ) && $totalPercentage != 100) {
                return redirect()->back()->with('error', "Total percentage for product '{$orderProduct->product_name}' must be exactly 100%. Current: {$totalPercentage}%");
            }
        }
    }

    // Process Blends (if provided)
    if (!empty($request->blends)) {
        foreach ($request->blends as $quoteProductId => $blendData) {
            $orderProduct = OrderProduct::find($quoteProductId);
            if (!$orderProduct) {
                continue;
            }

            $existingBlendIds = $orderProduct->items()
                ->where('item_type', 'blend')
                ->pluck('id')
                ->toArray();
            $newBlendIds = [];

          if (!empty($blendData['blend_id'])) {
           
            // If blend_id exists in request, check for existing and update/create
            $blend = Product::find($blendData['blend_id']);

            // Check if blend already exists for this product
            $existingItem = $orderProduct->items()
                ->where('item_type', 'blend')
                ->where('item_id', $blend->id)
                ->first();

            if ($existingItem) {
                // Update existing blend
                $existingItem->update([
                    'item_name' => $blend->name,
                    'unit' => $blend->unit_of_measure,
                    'unit_cost' => $blend->unit_price,
                    'quantity' => 0,
                    'total_cost' => 0
                ]);
                $newBlendIds[] = $existingItem->id;
            } else {
                // Create new blend
                $newItem = $orderProduct->items()->create([
                    'item_type' => 'blend',
                    'item_id' => $blend->id,
                    'item_name' => $blend->name,
                    'quantity' => 0,
                    'unit' => $blend->unit_of_measure,
                    'unit_cost' => $blend->unit_price,
                    'total_cost' => 0
                ]);
                $newBlendIds[] = $newItem->id;
            }

            // Remove any blend items not in the current request
            $orderProduct->items()
                ->where('item_type', 'blend')
                ->whereNotIn('id', $newBlendIds)
                ->delete();
            }
        }
    }

    return redirect()->route('orders.create', ['step' => 'packaging']);
}



public function addPackaging(Request $request, Quote $quote)
{
    $request->validate([
        'packaging' => 'required|array',
        'packaging.*.packaging' => 'required|array',
        'packaging.*.packaging.*.item_id' => 'required|exists:products,id',
    ]);

    foreach ($request->packaging as $quoteProductId => $packagingData) {
        $orderProduct = OrderProduct::find($quoteProductId);

        if (!$orderProduct) {
            continue; // Skip if quote product not found
        }

        $existingItemIds = $orderProduct->packagingItems()->pluck('id')->toArray();
        $newItemIds = [];

        foreach ($packagingData['packaging'] as $item) {
            $product = Product::find($item['item_id']);
            if (!$product) continue;

            // Check if this packaging already exists for the product
            $existingItem = $orderProduct->packagingItems()
                ->where('item_id', $product->id)
                ->first();

            if ($existingItem) {
                // Update existing packaging
                $existingItem->update([
                    'item_name' => $product->name,
                    'unit' => $product->unit_of_measure,
                    'unit_cost' => $product->unit_price,
                    'percentage' => 0,
                    'total_cost' => $product->unit_price,
                ]);
                $newItemIds[] = $existingItem->id;
            } else {
                // Create new packaging
                $newItem = $orderProduct->packagingItems()->create([
                    'item_type' => 'packaging',
                    'item_id' => $product->id,
                    'item_name' => $product->name,
                    'unit' => $product->unit_of_measure,
                    'percentage' => 0,
                    'unit_cost' => $product->unit_price,
                    'total_cost' => $product->unit_price,
                ]);
                $newItemIds[] = $newItem->id;
            }
        }

        // Remove any packaging items not in the current request
        $orderProduct->packagingItems()
            ->whereNotIn('id', $newItemIds)
            ->delete();
    }

    return redirect()
        ->route('orders.create', ['step' => 'calculation']);
}




public function calculate(Request $request, Order $order)
{
    $request->validate([
        'manufacturing_cost_percent' => 'nullable|numeric|min:0|max:100',
        'risk_cost_percent' => 'nullable|numeric|min:0|max:100',
        'profit_margin_percent' => 'nullable|numeric|min:0|max:100',
        'tax_rate' => 'nullable|numeric|min:0|max:100'
    ]);

    $manufacturingPercent = $request->manufacturing_cost_percent ?? 30;
    $riskPercent = $request->risk_cost_percent ?? 5;
    $profitPercent = $request->profit_margin_percent ?? 30;
    $taxRate = $request->tax_rate ?? 19;

    $quoteTotals = [
        'raw' => 0,
        'packaging' => 0,
        'manufacturing' => 0,
        'risk' => 0,
        'subtotal' => 0,
        'tax' => 0,
        'total' => 0
    ];

    foreach ($order->products as $orderProduct) {
        // Use packaging volume as final product volume
        $packagingItem = $orderProduct->packagingItems()->first();
        $productVolume = $packagingItem->itemDetail->volume ?? $orderProduct->final_product_volume;
        $packagingUnit = $packagingItem->itemDetail->unit_of_measure ?? 'kg'; // Get packaging unit

        // ----- Calculate raw materials -----
        $rawMaterialCost = 0;
        $rawMaterialItems = $orderProduct->rawMaterialItems ?? collect();

        if ($rawMaterialItems->count() > 0) {
            foreach ($rawMaterialItems as $item) {
                // Convert raw material unit to packaging unit
                $convertedQuantity = $this->convertUnit(
                    ($item->percentage / 100) * $productVolume,
                    $item->itemDetail->unit_of_measure ?? 'kg',
                    $packagingUnit
                );
                
                $cost = $convertedQuantity * $item->unit_cost;
                $item->update([
                    'quantity' => $convertedQuantity,
                    'total_cost' => $cost
                ]);

                $rawMaterialCost += $cost;
            }
        }
        // ----- Calculate blend -----
        $blendItem = $orderProduct->items()->where('item_type', 'blend')->first();
        if ($blendItem) {
            // Convert blend to packaging unit
            $convertedBlendVolume = $this->convertUnit(
                $productVolume,
                $blendItem->itemDetail->unit_of_measure ?? 'kg',
                $packagingUnit
            );
            
            $blendCost = $blendItem->itemDetail->unit_price * $convertedBlendVolume;
            $blendItem->update([
                'quantity' => $convertedBlendVolume,
                'total_cost' => $blendCost
            ]);
            $rawMaterialCost = $blendCost; // Override raw material if blend exists
        }

        // ----- Packaging cost -----
        $packagingCost = $orderProduct->packagingItems()->sum('total_cost');

        // Calculate product cost for entire quantity
        $productCost = $orderProduct->quantity * ($rawMaterialCost + $packagingCost);

        // ----- Manufacturing & Risk -----
        $manufacturingCost = ($manufacturingPercent / 100) * $productCost;
        $riskCost = ($riskPercent / 100) * $productCost;

        // ----- Subtotal, Profit & Tax -----
        $subtotal = $productCost + $manufacturingCost + $riskCost;
        $profitAmount = ($profitPercent / 100) * $subtotal;
        $totalBeforeTax = $subtotal + $profitAmount;
        $taxAmount = ($taxRate / 100) * $totalBeforeTax;
        $totalAmount = $totalBeforeTax + $taxAmount;

        // Calculate per-unit values
        $priceUnit = $totalAmount / $orderProduct->quantity;
        $subtotalUnit = $subtotal / $orderProduct->quantity;
        $manufacturingCostUnit = $manufacturingCost / $orderProduct->quantity;
        $riskCostUnit = $riskCost / $orderProduct->quantity;
        $taxAmountUnit = $taxAmount / $orderProduct->quantity;

        // ----- Update quote product -----
        $orderProduct->update([
            'raw_material_cost_unit' => $rawMaterialCost,
            'packaging_cost_unit' => $packagingCost,
            'packaging_cost_unit' => $packagingCost / $orderProduct->quantity,
            'risk_cost_unit' => $riskCostUnit,
            'profit_margin_unit' => $profitPercent,
            'subtotal' => $subtotalUnit,
            'tax_rate' => $taxRate,
            'tax_amount_unit' => $taxAmountUnit,
            'price_unit' => $priceUnit,
            'total_amount' => $totalAmount
        ]);

        // ----- Accumulate totals -----
        $quoteTotals['raw'] += $rawMaterialCost;
        $quoteTotals['packaging'] += $packagingCost;
        $quoteTotals['manufacturing'] += $manufacturingCost;
        $quoteTotals['risk'] += $riskCost;
        $quoteTotals['subtotal'] += $subtotal;
        $quoteTotals['tax'] += $taxAmount;
        $quoteTotals['total'] += $totalAmount;
    }

    // ----- Update main quote -----
    $order->update([
        'total_raw_material_cost' => round($quoteTotals['raw'],2),
        'total_packaging_cost' => round($quoteTotals['packaging'],2),
        'manufacturing_cost' => round($quoteTotals['manufacturing'],2),
        'risk_cost' => round($quoteTotals['risk'],2),
        'total_profit' => round($quoteTotals['subtotal'] * ($profitPercent / 100),2),
        'subtotal' => round($quoteTotals['subtotal'],2),
        'tax_amount' => round($quoteTotals['tax'],2),
        'total_amount' => round($quoteTotals['total'],2)
    ]);

    session()->forget('current_order_id');

    return redirect()->route('orders.show', $order->id)
        ->with('success', 'Order calculated and saved successfully');
}

/**
 * Convert between units: kg, g, mg
 * 
 * @param float $quantity
 * @param string $fromUnit
 * @param string $toUnit
 * @return float
 */
private function convertUnit($quantity, $fromUnit, $toUnit)
{
    // Conversion map to base unit (kg)
    $toKilogram = [
        'kg' => 1,
        'g'  => 0.001,
        'mg' => 0.000001
    ];

    // Validate units
    if (!isset($toKilogram[$fromUnit]) || !isset($toKilogram[$toUnit])) {
        return $quantity; // Return unchanged if unit is invalid
    }

    // If same unit, return as is
    if ($fromUnit === $toUnit) {
        return $quantity;
    }

    // Convert from source unit to kg, then to target unit
    $quantityInKg = $quantity * $toKilogram[$fromUnit];
    $convertedQuantity = $quantityInKg / $toKilogram[$toUnit];

    return $convertedQuantity;
}


 public function edit(Request $request,$id)
    {
        $customers = Customer::get();
        $order = Order::with(['products'])->findOrFail($id);
                $step = $request->step ?? 'basic';

                $rawMaterials = Product::where('category', 'raw_material')->get();
        $packagingMaterials = Product::where('category', 'packaging')->get();
        $blends = Product::where('category', 'blend')->get();

        return view('orders.edit', compact('customers', 'order','step','rawMaterials','packagingMaterials','blends'));
    }

    public function create(Request $request)
    {
        
      $customers = Customer::all();
        $rawMaterials = Product::where('category', 'raw_material')->get();
        $packagingMaterials = Product::where('category', 'packaging')->get();
        $blends = Product::where('category', 'blend')->get();
/*        Session::put('current_quote_id',null);
*/        
        $orderId = session('current_order_id');
        $order = $orderId ? Order::with(['products.items'])->find($orderId) : null;
        $step = $request->step ?? 'basic';
        return view('orders.create', compact('step', 'customers', 'rawMaterials', 'packagingMaterials', 'blends', 'order'));
    }

    public function store(Request $request)
    {
/*        echo json_encode($request->all()); exit;
*/        $request->validate([
            'qa_quotes_id' => 'required|exists:qa_quotes,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after:order_date',
            'order_notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.quote_product_id' => 'required|exists:quote_products,id',
            'items.*.quantity' => 'required|numeric|min:1'
        ]);

/*        echo 1; exit;
*/
        $qa = QaQuote::findOrFail($request->qa_quotes_id);
/*echo json_encode($qa); exit;
*/        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(8),
            'quote_id' => $qa->quote_id,
            'qa_quotes_id' => $qa->id,
            'order_date' => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'total_amount' => 0,
            'order_notes' => $request->order_notes,
            'status' => 'pending'
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $quoteProduct = $qa->quote->products()->find($item['quote_product_id']);
            
            if ($quoteProduct) {
                $totalPrice = $quoteProduct->total_amount * $item['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'quote_product_id' => $quoteProduct->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $quoteProduct->total_amount,
                    'total_price' => $totalPrice
                ]);

                $totalAmount += $totalPrice;
            }
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Order created successfully');
    }

    public function show($id)
    {
        $order = Order::with(['quote.customer', 'items', 'production'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function confirm($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Order confirmed');
    }
}
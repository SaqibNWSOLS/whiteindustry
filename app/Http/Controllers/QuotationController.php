<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteProduct;
use App\Models\QuoteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Customer;
use Session;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use App\Models\RndQuote;
 use Illuminate\Support\Facades\Validator;
 use App\Models\QaQuote;
 use App\Models\OrderProduct;
 use App\Models\OrderItem;

class QuotationController extends Controller
{
    public function create(Request $request)
    {
        $customers = Customer::all();
        $rawMaterials = Product::whereIn('category', ['raw_material','blend'])->get();
        $packagingMaterials = Product::where('category', 'packaging')->get();
        $quoteId = session('current_quote_id');
        $quote = $quoteId ? Quote::with(['products.items'])->find($quoteId) : null;
        $step = $request->step ?? 'basic';
        
        return view('quotes.create', compact('step', 'customers', 'rawMaterials', 'packagingMaterials', 'quote'));
    }

    public function storeBasic(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
        ]);

        // Create the main quote
        $quote = Quote::create([
            'quotation_number' => 'QT-' . Str::random(8),
            'customer_id' => $request->customer_id,
            'notes' => $request->notes,
            'status' => 'draft'
        ]);

        // Store quote ID in session for next steps
        session(['current_quote_id' => $quote->id]);

        return redirect()->route('quotes.create', ['step' => 'products'])
            ->with('success', 'Basic information saved successfully');
    }


     public function updateBasic(Request $request,$id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
        ]);


        $quote=Quote::where('id',$id)->first();

        // Create the main quote
        $quote = $quote->update([
            'quotation_number' => 'QT-' . Str::random(8),
            'customer_id' => $request->customer_id,
            'notes' => $request->notes,
            'status' => 'draft'
        ]);



        // Store quote ID in session for next steps
        session(['current_quote_id' => $id]);

        return redirect()->route('quotes.edit', ['quote' => $id, 'step' => 'products']);
    }

   public function addProducts(Request $request, Quote $quote)
{
    $request->validate([
        'products' => 'required|array',
        'products.*.product_name' => 'required|string|max:255',
        'products.*.quantity' => 'required',
        'products.*.product_type' => 'required|in:cosmetic,food_supplement',
    ]);

    $existingProductIds = $quote->products->pluck('id')->toArray();
    $newProductIds = [];

    foreach ($request->products as $productData) {
        if (isset($productData['id'])) {
            // ✅ Update existing product
            $quoteProduct = $quote->products()->find($productData['id']);
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
        $newProduct = $quote->products()->create([
            'product_name' => $productData['product_name'],
            'product_type' => $productData['product_type'],
            'quantity'=> $productData['quantity'],
            'tax_rate' => 19.00,
        ]);
        $newProductIds[] = $newProduct->id;
    }

    // ❌ Optional: Remove products not included in the request
    // $quote->products()->whereNotIn('id', $newProductIds)->delete();

    return redirect()->route('quotes.edit', [
        'quote' => $quote->id,
        'step' => 'raw_materials'
    ]);
}

 public function addRawMaterialsAndBlends(Request $request, $id)
{
    $request->validate([
        'raw_materials' => 'required|array',
        'raw_materials.*.materials' => 'required|array|min:1',
        'raw_materials.*.materials.*.item_id' => 'required|exists:products,id',
        'raw_materials.*.materials.*.percentage' => 'required|numeric|min:0.01|max:100',
    ]);


    $quote = Quote::findOrFail($id);

    // Process Raw Materials (if provided)
    if (!empty($request->raw_materials)) {
        foreach ($request->raw_materials as $quoteProductId => $productMaterials) {
            $quoteProduct = QuoteProduct::find($quoteProductId);
            if (!$quoteProduct) {
                continue;
            }

            $existingItemIds = $quoteProduct->rawMaterialItems->pluck('id')->toArray();
            $newItemIds = [];
            $totalPercentage = 0;

            foreach ($productMaterials['materials'] as $materialData) {
                $material = Product::find($materialData['item_id']);
                $totalPercentage += $materialData['percentage'];

                if (isset($materialData['id']) && !empty($material)) {
                    // Update existing raw material
                    $quoteItem = $quoteProduct->rawMaterialItems()->find($materialData['id']);
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
                   
                $newItem = $quoteProduct->rawMaterialItems()->create([
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
            $quoteProduct->rawMaterialItems()->whereNotIn('id', $newItemIds)->delete();

            // Validate total percentage
            if (!empty($material ) && $totalPercentage != 100) {
                return redirect()->back()->with('error', "Total percentage for product '{$quoteProduct->product_name}' must be exactly 100%. Current: {$totalPercentage}%");
            }
        }
    }


    return redirect()->route('quotes.create', ['step' => 'packaging']);
}



public function addPackaging(Request $request, Quote $quote)
{
    $request->validate([
        'packaging' => 'required|array',
        'packaging.*.packaging' => 'required|array',
        'packaging.*.packaging.*.item_id' => 'required|exists:products,id',
    ]);

    foreach ($request->packaging as $quoteProductId => $packagingData) {
        $quoteProduct = QuoteProduct::find($quoteProductId);

        if (!$quoteProduct) {
            continue; // Skip if quote product not found
        }

        $existingItemIds = $quoteProduct->packagingItems()->pluck('id')->toArray();
        $newItemIds = [];

        foreach ($packagingData['packaging'] as $item) {
            $product = Product::find($item['item_id']);
            if (!$product) continue;

            // Check if this packaging already exists for the product
            $existingItem = $quoteProduct->packagingItems()
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
                $newItem = $quoteProduct->packagingItems()->create([
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
        $quoteProduct->packagingItems()
            ->whereNotIn('id', $newItemIds)
            ->delete();
    }

    return redirect()
        ->route('quotes.create', ['step' => 'calculation']);
}



public function calculate(Request $request, Quote $quote)
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

    foreach ($quote->products as $quoteProduct) {
        // Use packaging volume as final product volume
        $packagingItem = $quoteProduct->packagingItems()->first();
        $productVolume = $packagingItem->itemDetail->volume ?? $quoteProduct->final_product_volume;
        $packagingUnit = $packagingItem->itemDetail->unit_of_measure ?? 'kg'; // Get packaging unit

        // ----- Calculate raw materials -----
        $rawMaterialCost = 0;
        $rawMaterialItems = $quoteProduct->rawMaterialItems ?? collect();

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
        

        // ----- Packaging cost -----
        $packagingCost = $quoteProduct->packagingItems()->sum('total_cost');

        // Calculate product cost for entire quantity
        $productCost = $quoteProduct->quantity * ($rawMaterialCost + $packagingCost);

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
        $priceUnitTax = $totalAmount / $quoteProduct->quantity;
        $price_unit = $totalBeforeTax / $quoteProduct->quantity;
        $manufacturingCostUnit = $manufacturingCost / $quoteProduct->quantity;
        $riskCostUnit = $riskCost / $quoteProduct->quantity;
        $taxAmountUnit = $taxAmount / $quoteProduct->quantity;

        // ----- Update quote product -----
        $quoteProduct->update([
            'raw_material_cost_unit' => $rawMaterialCost,
            'packaging_cost_unit' => $packagingCost,
            'manufacturing_cost_unit' => $manufacturingCostUnit,
            'risk_cost_unit' => $riskCostUnit,
            'profit_margin_unit' => $profitPercent,
            'price_unit_tax' => $priceUnitTax,
            'tax_rate' => $taxRate,
            'tax_amount_unit' => $taxAmountUnit,
            'price_unit' => $price_unit,
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
    $quote->update([
        'total_raw_material_cost' => round($quoteTotals['raw'],2),
        'total_packaging_cost' => round($quoteTotals['packaging'],2),
        'manufacturing_cost' => round($quoteTotals['manufacturing'],2),
        'risk_cost' => round($quoteTotals['risk'],2),
        'total_profit' => round($quoteTotals['subtotal'] * ($profitPercent / 100),2),
        'subtotal' => round($quoteTotals['subtotal'],2),
        'tax_amount' => round($quoteTotals['tax'],2),
        'total_amount' => round($quoteTotals['total'],2)
    ]);

    session()->forget('current_quote_id');

    return redirect()->route('quotes.show', $quote->id)
        ->with('success', 'Quotation calculated and saved successfully');
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


   public function index()
{
    $quotes = Quote::with(['customer', 'products.items'])->latest()->get();
    
    $stats = [
        'total' => Quote::count(),
        'draft' => Quote::where('status', 'draft')->count(),
        'sent' => Quote::where('status', 'sent')->count(),
        'accepted' => Quote::where('status', 'accepted')->count(),
        'rejected' => Quote::where('status', 'rejected')->count(),
        'completed' => Quote::where('status', 'completed')->count(),
    ];

    return view('quotes.index', compact('quotes', 'stats'));
}

    public function editModal(Request $request,$id)
    {
        $customers = Customer::get();
        $quote = Quote::with(['products'])->findOrFail($id);
                $step = $request->step ?? 'basic';

                $rawMaterials = Product::whereIn('category', ['raw_material','blend'])->get();
        $packagingMaterials = Product::where('category', 'packaging')->get();

        return view('quotes.edit', compact('customers', 'quote','step','rawMaterials','packagingMaterials'));
    }


public function show(Quote $quote)
{
    $quote->load([
        'customer',
        'products.rawMaterialItems.item',
        'products.packagingItems.item',
        'products.blendItems.item'
    ]);
    $rndDocuments=$quote->rndQuote->documents??[];
    $qaDocuments=$quote->orders->qaQuote->qaDocuments??[];

    return view('quotes.show', compact('quote','rndDocuments','qaDocuments'));
}
    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:draft,sent,accepted,rejected'
        ]);

        $quote->update($request->only(['notes', 'status']));

        return response()->json($quote->load(['customer', 'products.items']));
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return response()->json(null, 204);
    }

    public function removeItem($itemId)
    {
        $item = QuoteItem::findOrFail($itemId);
        $quoteProduct = $item->quoteProduct;
        
        $item->delete();

        // Recalculate costs if needed
        if (in_array($item->item_type, ['packaging', 'raw_material', 'blend'])) {
            // You might want to trigger a recalculation here
            // or let the user recalculate manually
        }

        return response()->json(['message' => 'Item removed successfully']);
    }

    // Helper method to get product details for AJAX requests
    public function getProductDetails($id)
    {
        $product = Product::findOrFail($id);
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'unit_price' => $product->unit_price,
            'unit_of_measure' => $product->unit_of_measure
        ]);
    }

    public function markAsAccepted(Quote $quote){

        if ($quote->status == 'accepted') {
            return redirect()->back()->with('error', 'Only un accepted can be marked as accepted');
        }

        $quote->update(['status' => 'accepted']);

           return redirect()->route('quotes.index')
            ->with('success', 'Quotation has been marked as accepted');

    }

    public function sendToRnd(Quote $quote)
    {
        // Check if already sent to R&D
        if ($quote->rndQuote) {
            return redirect()->back()->with('error', 'This quotation has already been sent to R&D');
        }

        // Check if quotation is accepted
     /*   if ($quote->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only accepted quotations can be sent to R&D');
        }*/

        // Create R&D Department entry
        $rnd = RndQuote::create([
            'quote_id' => $quote->id,
            'sent_at' => now(),
            'status' => 'pending'
        ]);

        // Update quote status
        $quote->update(['status' => 'sent_to_rnd']);

        return redirect()->route('quotes.index')
            ->with('success', 'Quotation sent to R&D department successfully');
    }

    public function downloadPDF($id)
    {
        $quote = Quote::with(['customer', 'products.packaging'])->findOrFail($id);
        
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))->setPaper('a4', 'portrait');
        
        $filename = 'quotation-' . ($quote->quote_number ?? $quote->id) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function convertToOrder($id){

        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT);
        $quote = Quote::where('id',$id)->first();

        // Create the main order
        $order = Order::create([
            'order_number' => $orderNumber,
            'quote_id' => $quote->id,
            'customer_id'=>$quote->customer_id,
            'rnd_quotes_id' => $quote->rndQuote->id ? $quote->rndQuote->id : null,
            'order_date' => now(),
            'delivery_date' => now()->addDays(30),
            'total_amount' => $quote->total_amount,
            'order_notes' => $quote->notes,
            'status' => 'pending'
        ]);

          QaQuote::create([
            'orders_id' => $order->id,
            'rnd_quotes_id' => $quote->rndQuote->id??''
        ]);

          // Create QA quote record
        

        // Copy quote products to order products
        foreach ($quote->products as $quoteProduct) {
            $orderProduct = OrderProduct::create([
                'quote_id' => $quote->id,
                'orders_id' => $order->id,
                'quote_product_id' => $quoteProduct->id,
                'product_name' => $quoteProduct->product_name,
                'product_type' => $quoteProduct->product_type,
                'raw_material_cost_unit' => $quoteProduct->raw_material_cost_unit,
                'packaging_cost_unit' => $quoteProduct->packaging_cost_unit,
                'manufacturing_cost_unit' => $quoteProduct->manufacturing_cost_unit,
                'risk_cost_unit' => $quoteProduct->risk_cost_unit,
                'profit_margin_unit' => $quoteProduct->profit_margin_unit,
                'price_unit_tax' => $quoteProduct->price_unit_tax,
                'quantity' => $quoteProduct->quantity,
                'tax_rate' => $quoteProduct->tax_rate,
                'tax_amount_unit' => $quoteProduct->tax_amount_unit,
                'total_amount' => $quoteProduct->total_amount,
                'price_unit' => $quoteProduct->price_unit,
                'final_product_volume' => $quoteProduct->final_product_volume,
                'volume_unit' => $quoteProduct->volume_unit,
            ]);

            // Copy quote items to order items
            foreach ($quoteProduct->items as $quoteItem) {
                OrderItem::create([
                    'order_products_id' => $orderProduct->id,
                    'quote_product_id' => $quoteProduct->id,
                    'quote_item_id' => $quoteItem->id,
                    'item_type' => $quoteItem->item_type,
                    'item_id' => $quoteItem->item_id,
                    'item_name' => $quoteItem->item_name,
                    'quantity' => $quoteItem->quantity,
                    'unit' => $quoteItem->unit,
                    'percentage' => $quoteItem->percentage,
                    'unit_cost' => $quoteItem->unit_cost,
                    'total_cost' => $quoteItem->total_cost,
                ]);
            }
        }
       

       return back()->with('success','Order has been placed successfully!');

    }


}
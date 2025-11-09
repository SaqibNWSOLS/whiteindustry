<?php

namespace App\Http\Controllers\Api;

use App\Models\Quote;
use App\Models\RawMaterial;
use App\Models\Packaging;
use App\Models\QuoteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with(['customer', 'products'])->latest()->get();
        return response()->json($quotes);
    }

    public function createModal(){

        $customers= Customer::get();

        return view('quotes.create',compact('customers'));
    }

      public function editModal($id){

        $customers= Customer::get();

        $quote=Quote::where('id',$id)->first();
        return view('quotes.edit',compact('customers','quote'));
    }

    public function rawMaterials(){

        $materials=Product::where('category','raw_material')->get();
         return response()->json($materials);
    }

    public function packaging(){

        $materials=Product::where('category','packaging')->get();
         return response()->json($materials);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|in:cosmetic,food_supplement',
        ]);

        $quote = Quote::create([
            'quote_number' => 'QT-' . Str::random(8),
            'customer_id' => $request->customer_id,
            'product_name' => $request->product_name,
            'product_type' => $request->product_type,
            'status' => 'draft'
        ]);

        return response()->json($quote->load('customer'), 201);
    }

    public function addRawMaterial(Request $request, $id)
    {
        $request->validate([
            'raw_material_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        $quote=Quote::where('id',$id)->first();

        $rawMaterial = Product::findOrFail($request->raw_material_id);

        // Check if total percentage exceeds 100%
        $currentTotalPercentage = $quote->rawMaterialItems()->sum('percentage');
        if ($currentTotalPercentage + $request->percentage > 100) {
            return response()->json([
                'error' => 'Total percentage cannot exceed 100%. Current total: ' . $currentTotalPercentage . '%'
            ], 422);
        }

        $item = $quote->items()->create([
            'item_type' => 'raw_material',
            'item_id' => $rawMaterial->id,
            'item_name' => $rawMaterial->name,
            'quantity' => 0, // Will be calculated later based on packaging volume
            'unit' => $rawMaterial->unit_of_measure,
            'percentage' => $request->percentage,
            'unit_cost' => $rawMaterial->unit_price,
            'total_cost' => $rawMaterial->unit_price/100*(float)$request->percentage // Will be calculated later
        ]);

        return response()->json($item);
    }

    public function addPackaging(Request $request, $id)
    {
        $request->validate([
            'packaging_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1'
        ]);
         $quote=Quote::where('id',$id)->first();

        $packaging = Product::findOrFail($request->packaging_id);

        $item = $quote->items()->create([
            'item_type' => 'packaging',
            'item_id' => $packaging->id,
            'item_name' => $packaging->name,
            'quantity' => $request->quantity,
            'unit' => $packaging->unit_of_measure,
            'percentage' => 0,
            'unit_cost' => $packaging->unit_price,
            'total_cost' => $packaging->unit_price * $request->quantity
        ]);

        // Update packaging cost
        $quote->update([
            'total_packaging_cost' => $quote->packagingItems()->sum('total_cost')
        ]);

        return response()->json($item);
    }

    public function calculate(Request $request, $id)
    {
        $request->validate([
            'manufacturing_cost_percent' => 'nullable|numeric|min:0|max:100',
            'risk_cost_percent' => 'nullable|numeric|min:0|max:100',
            'profit_margin_percent' => 'nullable|numeric|min:0|max:100',
            'tax_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        $quote=Quote::where('id',$id)->first();

        $manufacturingPercent = $request->manufacturing_cost_percent ?? 30;
        $riskPercent = $request->risk_cost_percent ?? 5;
        $profitPercent = $request->profit_margin_percent ?? 30;
        $taxRate = $request->tax_rate ?? 19;

        // Get packaging volume to calculate raw material quantities
        $packagingVolume = $quote->packagingItems()->sum('quantity');
        $quote->update(['final_product_volume' => $packagingVolume]);

        // Calculate raw material costs based on percentages and packaging volume
        $totalRawMaterialCost = 0;
        foreach ($quote->rawMaterialItems as $item) {
            $materialQuantity = ($item->percentage / 100) * $packagingVolume;
            $materialCost = $materialQuantity * $item->unit_cost;
            
            $item->update([
                'quantity' => $materialQuantity,
                'total_cost' => $materialCost
            ]);
            
            $totalRawMaterialCost += $materialCost;
        }

        $totalPackagingCost = $quote->packagingItems()->sum('total_cost');
        $manufacturingCost = ($manufacturingPercent / 100) * $totalRawMaterialCost;
        $riskCost = ($riskPercent / 100) * $totalRawMaterialCost;

        $subtotal = $totalRawMaterialCost + $totalPackagingCost + $manufacturingCost + $riskCost;
        $profitAmount = ($profitPercent / 100) * $subtotal;
        $totalWithoutTax = $subtotal + $profitAmount;
        $taxAmount = ($taxRate / 100) * $totalWithoutTax;
        $totalAmount = $totalWithoutTax + $taxAmount;

        $quote->update([
            'total_raw_material_cost' => $totalRawMaterialCost,
            'total_packaging_cost' => $totalPackagingCost,
            'manufacturing_cost' => $manufacturingCost,
            'risk_cost' => $riskCost,
            'profit_margin' => $profitPercent,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);

        $quote=Quote::where('id',$id)->first();
        return response()->json($quote);
    }

    public function show(Quote $quote)
    {
        return response()->json($quote->load(['customer', 'items']));
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'product_name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|in:draft,sent,accepted,rejected',
            'notes' => 'nullable|string'
        ]);

        $quote->update($request->all());

        return response()->json($quote);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return response()->json(null, 204);
    }
}
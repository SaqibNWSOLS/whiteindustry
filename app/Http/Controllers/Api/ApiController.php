<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QaQuote;
use App\Models\Quote;

class ApiController extends Controller
{
    /**
     * Get QA approved quotation and products
     */
    public function getQaProducts($qaId)
    {
        try {
            $qa = QaQuote::where('id', $qaId)
                ->where('status', 'approved')
                ->with(['quote.products'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'qa' => $qa,
                'products' => $qa->quote->products->map(function($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_type' => $product->product_type,
                        'total_amount' => $product->total_amount,
                        'subtotal' => $product->subtotal,
                        'tax_amount' => $product->tax_amount
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get production details by order
     */
    public function getProductionByOrder($orderId)
    {
        try {
            $order = Order::with(['production'])->findOrFail($orderId);
            
            return response()->json([
                'success' => true,
                'production' => $order->production
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Production not found'
            ], 404);
        }
    }
}
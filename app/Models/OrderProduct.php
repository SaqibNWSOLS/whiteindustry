<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'quote_id',
        'orders_id',
        'quote_product_id',
        'product_name',
        'product_type',
    'quantity',
    'raw_material_cost_unit',
    'packaging_cost_unit',
    'manufacturing_cost_unit',
    'risk_cost_unit',
    'profit_margin_unit',
    'subtotal',
    'tax_rate',
    'tax_amount_unit',
    'price_unit',
    'total_amount',
    'final_product_volume',
    'volume_unit',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }

    public function quoteProduct()
    {
        return $this->belongsTo(QuoteProduct::class, 'quote_product_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_products_id');
    }

    public function blendItems()
    {
        return $this->hasMany(OrderItem::class, 'order_products_id')->where('item_type', 'blend');
    }

    public function rawMaterialItems()
    {
        return $this->hasMany(OrderItem::class, 'order_products_id')
                    ->where('item_type', 'raw_material');
    }

    public function packagingItems()
    {
        return $this->hasMany(OrderItem::class, 'order_products_id')
                    ->where('item_type', 'packaging');
    }

    // New relationship for production tracking
    public function productionItems()
    {
        return $this->hasMany(ProductionItem::class);
    }

    // Get total production quantity for this product
    public function getTotalProductionQuantity()
    {
        return $this->productionItems()->sum('quantity_produced');
    }

    // Get total planned production
    public function getTotalPlannedProduction()
    {
        return $this->productionItems()->sum('quantity_planned');
    }

    // Check if production is complete
    public function isProductionComplete()
    {
        $planned = $this->getTotalPlannedProduction();
        $produced = $this->getTotalProductionQuantity();
        
        return $planned > 0 && $produced >= $planned;
    }
}
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
        'total_raw_material_cost',
        'total_packaging_cost',
        'manufacturing_cost',
        'risk_cost',
        'profit_margin',
        'subtotal',
        'quantity',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'final_product_volume',
        'volume_unit'
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'orders_id');
    }

    public function quoteProduct() {
        return $this->belongsTo(QuoteProduct::class, 'quote_product_id');
    }

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_products_id');
    }

    public function blendItems() {
        return $this->hasMany(OrderItem::class, 'order_products_id')->where('item_type', 'blend');
    }

    public function rawMaterialItems() {
        return $this->hasMany(OrderItem::class, 'order_products_id')
                    ->where('item_type', 'raw_material');
    }

    public function packagingItems() {
        return $this->hasMany(OrderItem::class, 'order_products_id')
                    ->where('item_type', 'packaging');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
     protected $fillable = [
        'order_products_id',
        'quote_product_id',
        'quote_item_id',
        'item_type',
        'item_id',
        'item_name',
        'quantity',
        'unit',
        'percentage',
        'unit_cost',
        'total_cost',
    ];

    public function orderProduct() {
        return $this->belongsTo(OrderProduct::class, 'order_products_id');
    }

    public function order() {
        return $this->hasOneThrough(Order::class, OrderProduct::class, 'id', 'id', 'order_products_id', 'orders_id');
    }

    public function itemDetail() {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function quoteItem() {
        return $this->belongsTo(QuoteItem::class, 'quote_item_id');
    }
}
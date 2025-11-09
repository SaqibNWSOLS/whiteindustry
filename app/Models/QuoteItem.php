<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_product_id',
        'item_type',
        'item_id',
        'item_name',
        'quantity',
        'unit',
        'percentage',
        'unit_cost',
        'total_cost'
    ];


 public function item()
    {
        return $this->belongsTo(QuoteProduct::class, 'quote_product_id');
    }

     public function itemDetail()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
    public function quoteProduct()
    {
        return $this->belongsTo(QuoteProduct::class, 'quote_product_id');
    }
}
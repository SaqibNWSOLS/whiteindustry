<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteProduct extends Model
{
    protected $fillable = [
        'quote_id',
        'product_name',
        'product_type',
        'total_raw_material_cost',
        'total_packaging_cost',
        'manufacturing_cost',
        'risk_cost',
        'profit_margin',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'final_product_volume',
        'volume_unit'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class, 'quote_product_id');
    }

     public function blendItems()
    {
        return $this->hasMany(QuoteItem::class, 'quote_product_id')->where('item_type', 'blend');
    }

    public function rawMaterialItems()
    {
        return $this->hasMany(QuoteItem::class, 'quote_product_id')
                    ->where('item_type', 'raw_material');
    }

     public function packaging()
    {
        return $this->hasOne(QuoteItem::class, 'quote_product_id')
                    ->where('item_type', 'packaging')->with('itemDetail');
    }

    public function packagingItems()
    {
        return $this->hasMany(QuoteItem::class, 'quote_product_id')
                    ->where('item_type', 'packaging');
    }
}
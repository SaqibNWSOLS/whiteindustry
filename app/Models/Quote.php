<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_number',
        'customer_id',
        'total_raw_material_cost',
        'total_packaging_cost',
        'manufacturing_cost',
        'risk_cost',
        'total_profit',
        'subtotal',
        'tax_amount',
        'total_amount',
        'notes',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->hasMany(QuoteProduct::class, 'quote_id');
    }

     public function rndQuote() {
        return $this->hasOne(RndQuote::class);
    }

    public function qaQuote() {
        return $this->hasOne(QaQuote::class);
    }

    public function orders() {
        return $this->hasOne(Order::class);
    }

   
}
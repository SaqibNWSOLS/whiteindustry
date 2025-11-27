<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 
        'customer_id',
        'quote_id', 
        'rnd_quotes_id', 
        'order_date', 
        'delivery_date', 
        'total_amount', 
        'order_notes', 
        'status'
    ];
    
    protected $dates = ['order_date', 'delivery_date'];

    public function quote() {
        return $this->belongsTo(Quote::class);
    }

     public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function rndQuote() {
        return $this->belongsTo(RndQuote::class, 'rnd_quotes_id');
    }

    public function products() {
        return $this->hasMany(OrderProduct::class, 'orders_id');
    }

    public function items() {
        return $this->hasManyThrough(OrderItem::class, OrderProduct::class, 'orders_id', 'order_products_id');
    }

    public function production() {
        return $this->hasOne(Production::class);
    }
}
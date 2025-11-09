<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'customer_id', 'product_id', 'quantity', 'unit',
        'total_value', 'order_date', 'delivery_date', 'priority', 'status', 'special_instructions',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'total_value' => 'decimal:2',
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Generate a reasonably collision-resistant sequential order number per year.
        // We use the current max numeric suffix rather than a simple count to avoid
        // duplicates when rows are soft-deleted. Note this is still vulnerable to
        // a tiny race window; the controller will attempt retries on duplicate-key errors.
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $year = date('Y');
                // extract the numeric suffix from order_number and take the max
                $max = static::whereYear('created_at', $year)
                    ->selectRaw("MAX(CAST(SUBSTRING_INDEX(order_number, '-', -1) AS UNSIGNED)) as max_suffix")
                    ->value('max_suffix');

                $next = ($max ? intval($max) : 0) + 1;
                $order->order_number = 'ORD-' . $year . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function productionOrder()
    {
        return $this->hasOne(ProductionOrder::class);
    }
}
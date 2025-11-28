<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_transactions';

    protected $fillable = [
        'product_id',
        'production_item_id',
        'transaction_type',
        'quantity_change',
        'reference_type',
        'reference_id',
        'status',
        'notes',
        'created_by',
        'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productionItem()
    {
        return $this->belongsTo(ProductionItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public static function recordProduction($productionItem, $quantityChange, $status, $notes = null)
    {
        return self::create([
            'product_id' => $productionItem->orderProduct->product_id,
            'production_item_id' => $productionItem->id,
            'transaction_type' => 'production',
            'quantity_change' => $quantityChange,
            'reference_type' => 'production_item',
            'reference_id' => $productionItem->id,
            'status' => $status,
            'notes' => $notes,
            'created_by' => auth()?->id(),
            'transaction_date' => now()
        ]);
    }

    public static function recordSale($product, $quantityChange, $referenceType, $referenceId, $notes = null)
    {
        return self::create([
            'product_id' => $product->id,
            'transaction_type' => 'sale',
            'quantity_change' => -abs($quantityChange),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'notes' => $notes,
            'created_by' => auth()?->id(),
            'transaction_date' => now()
        ]);
    }

    public static function recordAdjustment($product, $quantityChange, $reason, $notes = null)
    {
        return self::create([
            'product_id' => $product->id,
            'transaction_type' => 'adjustment',
            'quantity_change' => $quantityChange,
            'reference_type' => 'adjustment',
            'status' => 'completed',
            'notes' => $notes ?? $reason,
            'created_by' => auth()?->id(),
            'transaction_date' => now()
        ]);
    }

    public static function recordDamage($product, $quantityDamaged, $notes = null)
    {
        return self::create([
            'product_id' => $product->id,
            'transaction_type' => 'damage',
            'quantity_change' => -abs($quantityDamaged),
            'status' => 'completed',
            'notes' => $notes,
            'created_by' => auth()?->id(),
            'transaction_date' => now()
        ]);
    }

    public static function recordReturn($product, $quantityReturned, $referenceType, $referenceId, $notes = null)
    {
        return self::create([
            'product_id' => $product->id,
            'transaction_type' => 'return',
            'quantity_change' => $quantityReturned,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'notes' => $notes,
            'created_by' => auth()?->id(),
            'transaction_date' => now()
        ]);
    }

    // Scopes
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeProduction($query)
    {
        return $query->where('transaction_type', 'production');
    }

    public function scopeSale($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('transaction_date', '>=', now()->subDays($days));
    }

    // Accessors
    public function getTransactionTypeNameAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->transaction_type));
    }

    public function getIsPositiveAttribute()
    {
        return $this->quantity_change >= 0;
    }

    public function getIsNegativeAttribute()
    {
        return $this->quantity_change < 0;
    }
}
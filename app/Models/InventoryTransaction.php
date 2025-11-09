<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id', 'type', 'quantity', 'reference_type',
        'reference_id', 'notes', 'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            $inventory = $transaction->inventory;
            
            if ($transaction->type === 'in') {
                $inventory->current_stock += $transaction->quantity;
            } else {
                $inventory->current_stock -= $transaction->quantity;
            }
            
            $inventory->updateStatus();
        });
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}

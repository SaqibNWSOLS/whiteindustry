<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'order_product_id',
        'products_id',
        'quantity_planned',
        'quantity_produced',
        'quantity_deliverd',
        'quantity_rejected',
        'notes',
        'status'
    ];

    protected $casts = [
        'quantity_planned' => 'integer',
        'quantity_produced' => 'integer',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class,'order_product_id');
    }

    public function getProgressPercentage()
    {
        if ($this->quantity_planned == 0) {
            return 0;
        }
        return round(($this->quantity_produced / $this->quantity_planned) * 100, 2);
    }

    public function inventoryTransactions()
{
    return $this->hasMany(InventoryTransaction::class, 'production_item_id');
}
}
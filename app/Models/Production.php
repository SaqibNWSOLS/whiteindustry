<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_number',
        'order_id',
        'start_date',
        'end_date',
        'production_notes',
        'status'
    ];

    protected $dates = ['start_date', 'end_date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionItem::class)->with('orderProduct');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTotalQuantityPlanned()
    {
        return $this->items()->sum('quantity_planned');
    }

    public function getTotalQuantityProduced()
    {
        return $this->items()->sum('quantity_produced');
    }

    public function getProductionProgress()
    {
        $planned = $this->getTotalQuantityPlanned();
        if ($planned == 0) {
            return 0;
        }
        return round(($this->getTotalQuantityProduced() / $planned) * 100, 2);
    }
}
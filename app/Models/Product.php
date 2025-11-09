<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_code', 'name', 'description', 'category', 'product_type',
        'unit_price', 'unit_of_measure', 'status','volume',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    /**
     * Unit lists by product type
     */
    public static $units = [

        // 🔹 Raw Material Units
        'raw_material' => [
            'kg' => 'Kilogram (kg)',
            'g'  => 'Gram (g)',
            'mg' => 'Milligram (mg)',
        ],

        // 🔹 Packing Material Units
        'packing' => [
            'kg' => 'Kilogram (kg)',
            'g'  => 'Gram (g)',
            'mg' => 'Milligram (mg)',
        ],

        // 🔹 Blend / Mixture Units
        'blend' => [
           'kg' => 'Kilogram (kg)',
            'g'  => 'Gram (g)',
            'mg' => 'Milligram (mg)',
        ],

        // 🔹 Final Product Units
        'final_product' => [
           'kg' => 'Kilogram (kg)',
            'g'  => 'Gram (g)',
            'mg' => 'Milligram (mg)',
        ],
    ];

    /**
     * Get units for a given product type
     */
    public static function getUnitsByType($type)
    {
        return self::$units[$type] ?? [];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }
}

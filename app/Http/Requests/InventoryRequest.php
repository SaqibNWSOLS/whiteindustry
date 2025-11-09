<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'material_code' => 'required|string|unique:inventory,material_code,' . $this->route('inventory')?->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:raw_material,packaging,final_product,blend',
            'category' => 'nullable|string|max:100',
            'current_stock' => 'required|numeric|min:0',
            // initial_stock is optional and used by some flows to seed inventory via a transaction
            'initial_stock' => 'nullable|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'composition' => 'nullable|array',
            'composition.*.inventory_id' => 'nullable|integer|exists:inventory,id',
            'composition.*.percentage' => 'nullable|numeric|min:0|max:100',
            'composition.*.quantity' => 'nullable|numeric|min:0',
            'packaging_volume' => 'nullable|numeric|min:0',
            'commission_percent' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'storage_location' => 'nullable|string|max:255',
        ];
    }
}

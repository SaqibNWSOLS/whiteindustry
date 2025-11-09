<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_code' => 'required|string|unique:products,product_code,' . $this->route('product')?->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:raw_material,packaging,final_product',
            'product_type' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'unit_of_measure' => 'required|string|max:50',
            'status' => 'nullable|in:active,inactive,discontinued',
        ];
    }
}
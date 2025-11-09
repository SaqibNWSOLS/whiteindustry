<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'total_value' => 'required|numeric|min:0',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after:order_date',
            'priority' => 'nullable|in:normal,high,urgent',
            'status' => 'nullable|in:pending,in_production,completed,cancelled',
            'special_instructions' => 'nullable|string',
        ];
    }
}

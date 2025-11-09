<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'nullable|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'production_line' => 'nullable|string|max:100',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,mixing,filling,packaging,qc_review,completed',
            'notes' => 'nullable|string',
        ];
    }
}


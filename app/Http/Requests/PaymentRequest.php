<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:bank_transfer,credit_card,check,cash,wire_transfer',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}

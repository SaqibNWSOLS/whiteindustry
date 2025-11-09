<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'type' => 'required|in:person,business',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $this->route('customer')?->id,
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive',
        ];

        if ($this->type === 'business') {
            $rules['company_name'] = 'required|string|max:255';
            $rules['industry_type'] = 'nullable|string|max:100';
            $rules['tax_id'] = 'nullable|string|max:50';
        }

        return $rules;
    }
}
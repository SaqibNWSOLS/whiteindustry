{{-- resources/views/crm/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('crm.edit.title', ['type' => __("crm.types.{$customer->type}")]))
@section('page_title', __('crm.edit.page_title', ['type' => __("crm.types.{$customer->type}")]))

@section('content')
<div class="content">
    <div style="max-width: 600px; margin: 0 auto;">
        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.company_name') }}</label>
                <input type="text" name="company_name" class="form-input @error('company_name') is-invalid @enderror" required value="{{ old('company_name', $customer->company_name) }}">
                @error('company_name') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.contact_person') }}</label>
                <input type="text" name="contact_person" class="form-input @error('contact_person') is-invalid @enderror" required value="{{ old('contact_person', $customer->contact_person) }}">
                @error('contact_person') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.email') }}</label>
                <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" required value="{{ old('email', $customer->email) }}">
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.phone') }}</label>
                <input type="text" name="phone" class="form-input @error('phone') is-invalid @enderror" required value="{{ old('phone', $customer->phone) }}">
                @error('phone') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            @if($customer->type === 'customer')
                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.address') }}</label>
                    <input type="text" name="address" class="form-input" value="{{ old('address', $customer->address) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.city') }}</label>
                    <input type="text" name="city" class="form-input" value="{{ old('city', $customer->city) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.postal_code') }}</label>
                    <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code', $customer->postal_code) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.industry_type') }}</label>
                    <select name="industry_type" class="form-input">
                        <option value="">{{ __('crm.form.select_industry') }}</option>
                        @foreach(__('crm.industry_types') as $key => $value)
                            <option value="{{ $key }}" {{ old('industry_type', $customer->industry_type) === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.tax_id') }}</label>
                    <input type="text" name="tax_id" class="form-input" value="{{ old('tax_id', $customer->tax_id) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.status') }}</label>
                    <select name="status" class="form-input">
                        <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>{{ __('crm.status.active') }}</option>
                        <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>{{ __('crm.status.inactive') }}</option>
                    </select>
                </div>
            @else
                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.source') }}</label>
                    <select name="source" class="form-input">
                        <option value="">{{ __('crm.form.select_source') }}</option>
                        @foreach(__('crm.sources') as $key => $value)
                            <option value="{{ $key }}" {{ old('source', $customer->source) === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.status') }}</label>
                    <select name="status" class="form-input">
                        @foreach(__('crm.lead_status') as $key => $value)
                            <option value="{{ $key }}" {{ old('status', $customer->status) === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.estimated_value') }}</label>
                    <input type="number" name="estimated_value" class="form-input" step="0.01" min="0" value="{{ old('estimated_value', $customer->estimated_value) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.notes') }}</label>
                    <textarea name="notes" class="form-input" rows="4">{{ old('notes', $customer->notes) }}</textarea>
                </div>

                <div class="alert alert-info">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="convert_to_customer" value="1">
                        <span>{{ __('crm.messages.convert_to_customer') }}</span>
                    </label>
                </div>
            @endif

            <div style="display: flex; gap: 12px; margin-top: 24px; border-top: 1px solid #e5e5e5; padding-top: 20px;">
                <button type="submit" class="btn btn-primary">{{ __('crm.buttons.update') }}</button>
                <a href="{{ route('customers.index', ['type' => $customer->type]) }}" class="btn btn-secondary">{{ __('crm.buttons.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
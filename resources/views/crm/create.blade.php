{{-- resources/views/crm/create.blade.php --}}
@extends('layouts.app')

@section('title', __('crm.create.title', ['type' => __("crm.types.$type")]))
@section('page_title', __('crm.create.page_title', ['type' => __("crm.types.$type")]))

@section('content')
<div class="content">
    <div style="max-width: 600px; margin: 0 auto;">

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.company_name') }}</label>
                <input type="text" name="company_name" class="form-input @error('company_name') is-invalid @enderror" required value="{{ old('company_name') }}">
                @error('company_name') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.contact_person') }}</label>
                <input type="text" name="contact_person" class="form-input @error('contact_person') is-invalid @enderror" required value="{{ old('contact_person') }}">
                @error('contact_person') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.email') }}</label>
                <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('crm.form.phone') }}</label>
                <input type="text" name="phone" class="form-input @error('phone') is-invalid @enderror" required value="{{ old('phone') }}">
                @error('phone') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            @if($type === 'customer')
                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.address') }}</label>
                    <input type="text" name="address" class="form-input" value="{{ old('address') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.city') }}</label>
                    <input type="text" name="city" class="form-input" value="{{ old('city') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.postal_code') }}</label>
                    <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code') }}">
                    @error('postal_code') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.industry_type') }}</label>
                    <select name="industry_type" class="form-input">
                        <option value="">{{ __('crm.form.select_industry') }}</option>
                        @foreach(__('crm.industry_types') as $key => $value)
                            <option value="{{ $key }}" {{ old('industry_type') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.tax_id') }}</label>
                    <input type="text" name="tax_id" class="form-input" value="{{ old('tax_id') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.status') }}</label>
                    <select name="status" class="form-input">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('crm.status.active') }}</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('crm.status.inactive') }}</option>
                    </select>
                </div>
            @else
                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.source') }}</label>
                    <select name="source" class="form-input">
                        <option value="">{{ __('crm.form.select_source') }}</option>
                        @foreach(__('crm.sources') as $key => $value)
                            <option value="{{ $key }}" {{ old('source') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.status') }}</label>
                    <select name="status" class="form-input">
                        @foreach(__('crm.lead_status') as $key => $value)
                            <option value="{{ $key }}" {{ old('status') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.estimated_value') }}</label>
                    <input type="number" name="estimated_value" class="form-input" step="0.01" min="0" value="{{ old('estimated_value') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('crm.form.notes') }}</label>
                    <textarea name="notes" class="form-input" rows="4">{{ old('notes') }}</textarea>
                </div>
            @endif

            <div style="display: flex; gap: 12px; margin-top: 24px; border-top: 1px solid #e5e5e5; padding-top: 20px;">
                <button type="submit" class="btn btn-primary">{{ __('crm.buttons.create') }}</button>
                <a href="{{ route('customers.index', ['type' => $type]) }}" class="btn btn-secondary">{{ __('crm.buttons.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
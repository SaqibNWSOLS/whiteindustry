
{{-- resources/views/crm/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create ' . ucfirst($type))
@section('page_title', 'Create ' . ucfirst($type))

@section('content')
<div class="content">
    <div style="max-width: 600px; margin: 0 auto;">

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="form-group">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-input @error('company_name') is-invalid @enderror" required value="{{ old('company_name') }}">
                @error('company_name') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Contact Person</label>
                <input type="text" name="contact_person" class="form-input @error('contact_person') is-invalid @enderror" required value="{{ old('contact_person') }}">
                @error('contact_person') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-input @error('phone') is-invalid @enderror" required value="{{ old('phone') }}">
                @error('phone') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            @if($type === 'customer')
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-input" value="{{ old('address') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-input" value="{{ old('city') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Postal Code</label>
                    <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code') }}">
                    @error('postal_code') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Industry Type</label>
                    <select name="industry_type" class="form-input">
                        <option value="">Select industry</option>
                        <option value="Cosmetics & Beauty" {{ old('industry_type') === 'Cosmetics & Beauty' ? 'selected' : '' }}>Cosmetics & Beauty</option>
                        <option value="Pharmaceuticals" {{ old('industry_type') === 'Pharmaceuticals' ? 'selected' : '' }}>Pharmaceuticals</option>
                        <option value="Dietary Supplements" {{ old('industry_type') === 'Dietary Supplements' ? 'selected' : '' }}>Dietary Supplements</option>
                        <option value="Other" {{ old('industry_type') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tax ID</label>
                    <input type="text" name="tax_id" class="form-input" value="{{ old('tax_id') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            @else
                <div class="form-group">
                    <label class="form-label">Source</label>
                    <select name="source" class="form-input">
                        <option value="">Select source</option>
                        <option value="website" {{ old('source') === 'website' ? 'selected' : '' }}>Website</option>
                        <option value="referral" {{ old('source') === 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="trade_show" {{ old('source') === 'trade_show' ? 'selected' : '' }}>Trade Show</option>
                        <option value="cold_call" {{ old('source') === 'cold_call' ? 'selected' : '' }}>Cold Call</option>
                        <option value="social_media" {{ old('source') === 'social_media' ? 'selected' : '' }}>Social Media</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="new" {{ old('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ old('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="proposal" {{ old('status') === 'proposal' ? 'selected' : '' }}>Proposal</option>
                        <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Estimated Value</label>
                    <input type="number" name="estimated_value" class="form-input" step="0.01" min="0" value="{{ old('estimated_value') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-input" rows="4">{{ old('notes') }}</textarea>
                </div>
            @endif

            <div style="display: flex; gap: 12px; margin-top: 24px; border-top: 1px solid #e5e5e5; padding-top: 20px;">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('customers.index', ['type' => $type]) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
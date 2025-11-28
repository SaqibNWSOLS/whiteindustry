@extends('layouts.app')
@section('title', 'Add Payment')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>Record Payment for Invoice #{{ $invoice->invoice_number }}</h2>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="invoice-summary">
                        <p><strong>Total Amount:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
                        <p><strong>Paid Amount:</strong> <span class="text-success">${{ number_format($invoice->paid_amount, 2) }}</span></p>
                        <p><strong>Pending Amount:</strong> <span class="text-danger">${{ number_format($invoice->pending_amount, 2) }}</span></p>
                    </div>
                </div>
            </div>

            <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="amount" class="form-label">Payment Amount *</label>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                           id="amount" name="amount" step="0.01" min="0.01" 
                           max="{{ $invoice->pending_amount }}" placeholder="0.00" value="{{ old('amount') }}" required>
                    <small class="text-muted">Max: ${{ number_format($invoice->pending_amount, 2) }}</small>
                    @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="payment_date" class="form-label">Payment Date *</label>
                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                           id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    @error('payment_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="payment_method" class="form-label">Payment Method *</label>
                    <select class="form-control @error('payment_method') is-invalid @enderror" 
                            id="payment_method" name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                    @error('payment_method')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="transaction_id" class="form-label">Transaction ID / Reference</label>
                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                           id="transaction_id" name="transaction_id" placeholder="e.g., TXN123456" value="{{ old('transaction_id') }}">
                    @error('transaction_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Record Payment</button>
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
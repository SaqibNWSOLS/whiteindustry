{{-- resources/views/crm/index.blade.php --}}
@extends('layouts.app')

@section('title', 'CRM')
@section('page_title', 'CRM')

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <a href="{{ route('customers.index', ['type' => 'customer']) }}" class="tab-button ">
                <i class="ti ti-users"></i> Customers
            </a>
            <a href="{{ route('customers.index', ['type' => 'lead']) }}" class="tab-button ">
                <i class="ti ti-target"></i> Leads
            </a>
            <a href="{{ route('quotes.index') }}" class="tab-button {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                <i class="ti ti-file-text"></i> Quotes
            </a>
        </div>
    </div>

   {{-- QUOTES TAB --}}
@if(request()->routeIs('quotes.index'))
    <div id="quotes-tab" class="tab-content active">

        <div class="module-header">
            <div style="display: flex; gap: 8px; align-items: center;">
                <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                    <i class="ti ti-file-plus"></i> New Quote
                </a>
            </div>

            <form method="GET" style="display: flex; gap: 8px;">
                <input type="search" name="q" class="search-input" placeholder="Search quotes...">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <div class="table-header">
                <h3>Quotes</h3>
                <div style="font-size: 0.875rem; color: #666;">
                    Showing {{ $quotes->count() }} records
                </div>
            </div>

            @if($quotes->isEmpty())
                <div class="alert alert-info">No quotes found</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Quotation #</th>
                            <th>Customer</th>
                            <th>Products</th>
                            <th>Subtotal</th>
                            <th>Tax</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($quotes as $quote)
                            <tr>
                                <td>{{ $quote->quotation_number }}</td>

                                <td>
                                    {{ $quote->customer->company_name ?? 'N/A' }} <br>
                                    <small>{{ $quote->customer->contact_person ?? '' }}</small>
                                </td>

                                <td>
                                    {{ $quote->products->count() }} item(s)
                                </td>

                                <td>${{ number_format($quote->subtotal, 2) }}</td>
                                <td>${{ number_format($quote->tax_amount, 2) }}</td>
                                <td><strong>${{ number_format($quote->total_amount, 2) }}</strong></td>

                                <td>
                                    <span class="badge 
                                        {{ $quote->status === 'approved' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-sm btn-secondary">
                                        View
                                    </a>

                                    <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('quotes.destroy', $quote->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('Delete this quote?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
@endif

</div>
@endsection

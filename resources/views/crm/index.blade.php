{{-- resources/views/crm/index.blade.php --}}
@extends('layouts.app')

@section('title', 'CRM')
@section('page_title', 'CRM')

@section('content')
<div class="content">
    <div class="tabs">
        <div class="tab-nav">
            <a href="{{ route('customers.index', ['type' => 'customer']) }}" class="tab-button {{ $type === 'customer' ? 'active' : '' }}">
                <i class="ti ti-users"></i> Customers
            </a>
            <a href="{{ route('customers.index', ['type' => 'lead']) }}" class="tab-button {{ $type === 'lead' ? 'active' : '' }}">
                <i class="ti ti-target"></i> Leads
            </a>
            <a href="{{ route('quotes.index') }}" class="tab-button {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                <i class="ti ti-file-text"></i> Quotes
            </a>
        </div>
    </div>

    @if($type === 'customer')
        {{-- CUSTOMERS TAB --}}
        <div id="customers-tab" class="tab-content active">
            <div class="module-header">
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('customers.create', ['type' => 'customer']) }}" class="btn btn-primary">
                        <i class="ti ti-user-plus"></i> Add Customer
                    </a>
                    <a href="{{ route('customers.export', ['type' => 'customer']) }}" class="btn btn-secondary">
                        <i class="ti ti-download"></i> Export
                    </a>
                </div>
                <form method="GET" style="display: flex; gap: 8px;">
                    <input type="hidden" name="type" value="customer">
                    <input type="search" name="q" class="search-input" placeholder="Search customers..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <div class="table-container">
                <div class="table-header">
                    <h3>Customer Database</h3>
                    <div style="font-size: 0.875rem; color: #666;">
                        Showing {{ $customers->count() }} of {{ $customers->total() }} records
                    </div>
                </div>

                @if($customers->isEmpty())
                    <div class="alert alert-info">No customers found</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->company_name }}</td>
                                    <td>{{ $item->contact_person }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->city ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customers.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" action="{{ route('customers.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($customers->hasPages())
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

    @elseif($type === 'lead')
        {{-- LEADS TAB --}}
        <div id="leads-tab" class="tab-content active">
            <div class="module-header">
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('customers.create', ['type' => 'lead']) }}" class="btn btn-primary">
                        <i class="ti ti-user-plus"></i> Add Lead
                    </a>
                    <a href="{{ route('customers.export', ['type' => 'lead']) }}" class="btn btn-secondary">
                        <i class="ti ti-download"></i> Export
                    </a>
                </div>
                <form method="GET" style="display: flex; gap: 8px;">
                    <input type="hidden" name="type" value="lead">
                    <input type="search" name="q" class="search-input" placeholder="Search leads..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <div class="table-container">
                <div class="table-header">
                    <h3>Sales Leads</h3>
                    <div style="font-size: 0.875rem; color: #666;">
                        Showing {{ $customers->count() }} of {{ $customers->total() }} records
                    </div>
                </div>

                @if($customers->isEmpty())
                    <div class="alert alert-info">No leads found</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Lead ID</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Source</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->company_name }}</td>
                                    <td>{{ $item->contact_person }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->source ?? '-' }}</td>
                                    <td>${{ number_format($item->estimated_value ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $item->status === 'qualified' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customers.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" action="{{ route('customers.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($customers->hasPages())
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
